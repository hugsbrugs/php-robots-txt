<?php

namespace Hug\Robots;

use Hug\FileSystem\FileSystem as FileSystem;

/**
 *
 */
class Robots
{
	/**
	 * Get robots.txt from cache if defined or from web
	 *
	 * @param string $url
	 * @param string $user_agent
	 * @return string $robots
	 */
	public static function get_robots($url, $user_agent)
	{
		$robots = null;
		
		$parsed_url = parse_url($url);

		if(defined('HUG_ROBOTS_CACHE_PATH'))
		{
			$robots_cache = HUG_ROBOTS_CACHE_PATH . $parsed_url['host'] . '.txt';
			$is_cache_obsolete = Robots::is_cache_obsolete($robots_cache);

			if(!defined('HUG_ROBOTS_CACHE_DURATION'))
			{
				# One month
				define('HUG_ROBOTS_CACHE_DURATION', 30*86400);
			}
			
			if(file_exists($robots_cache) && !$is_cache_obsolete)
			{
				$robots = gzuncompress(file_get_contents($robots_cache));
				// error_log('Use cache');
			}
		}

		if($robots===null)
		{
			
			$robots_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . '/robots.txt';
			// error_log('robots_url : ' . $robots_url);

			if(false===$robots = Robots::download_robots($robots_url, $user_agent))
			{
				$robots = '';	
			}

			if(defined('HUG_ROBOTS_CACHE_PATH') && $is_cache_obsolete)
			{
				file_put_contents($robots_cache, gzcompress($robots));
				// error_log('save cache');
			}
		}

		return $robots;
	}

	/**
	 * Download Robots.txt file throught CURL request
	 *
	 * @param string $url
	 * @param string $user_agent
	 * @return string $robots
	 */
	public static function is_cache_obsolete($file)
	{
		$is_cache_obsolete = true;
		
		$now = new \DateTime('now');
		
		// file_last_mod
		$file_last_mod = FileSystem::file_last_mod($file, $date_format = 'Y-m-d H:i:s');
		$file_last_mod = new \DateTime($file_last_mod);
		$file_last_mod->add(new \DateInterval('PT'.HUG_ROBOTS_CACHE_DURATION.'S'));

		if($now<$file_last_mod)
		{
			$is_cache_obsolete = false;
		}

		return $is_cache_obsolete;
	}	

	/**
	 * Download Robots.txt file throught CURL request
	 *
	 * @param string $url
	 * @param string $user_agent
	 * @return string $robots
	 */
	public static function download_robots($url, $user_agent = null)
	{
		$robots = '';

		$curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

        # SET USER AGENT
        if(!empty($user_agent))
        {
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
        }

        # Enable Debug if request fails to see if remote server
        # sends an explicit message about some missing header
        // curl_setopt($curl, CURLOPT_VERBOSE, true);

        $robots = curl_exec($curl);

        return $robots;
	}

	/**
	 * https://www.jugglingedge.com/help/creatingagoodbot.php
	 *
	 * Original PHP code by Chirp Internet: www.chirp.com.au
	 * 
	 * @param string $url
	 * @param string $user_agent
	 * @return bool $is_allowed
	 */
	public static function is_allowed($url, $user_agent = null)
	{
		$robots = Robots::get_robots($url, $user_agent);
		
		# Site has no robots.txt file, ok to continue
		if(empty($robots))
			return true;

		$robots = explode("\n", $robots);

		# Escape user agent name for use in regexp just in case
	    $user_agent = preg_quote($user_agent,'/');

		# Get list of rules that apply to us
		$rules = [];
		$applies = false;
		foreach($robots as $line)
		{
			// skip blanks & comments
			if(trim($line)=='' || $line[0]=='#')
				continue;

			if(preg_match('/^\s*User-agent:\s*(.*)/i', $line, $Match))
			{
				// Found start of a User-agent block, check if
				// it applies to all bots, or our specific bot
	            $applies = preg_match("/(\*|$user_agent)/i",$Match[1]);
	            continue;
			}

			if($applies)
			{
				// Add rules to our $rules array
				list($type, $rule) = explode(':', $line, 2);
				$type = trim(strtolower($type));
				// Allow or Disallow
				$rules[] = ['type'=>$type,'Match'=>preg_quote(trim($rule),'/')];
			}
		}

		// Check url against our list of rules

	    $parsed_url = parse_url($url);

		$allowed = true;
		$max_length = 0;
		foreach($rules as $rule)
		{
			if(preg_match('/^'.$rule['Match'].'/',$parsed_url['path']))
			{
				// Specified rule applies to the url we are checking
				// Longer rules > Shorter rules
				// Allow > Disallow if rules same length
				$this_length = strlen($rule['Match']);
				if($max_length < $this_length)
				{
					$allowed = ($rule['type']=='allow');
					$max_length = $this_length;
				}
				elseif($max_length==$this_length && $rule['type']=='allow')
				{
					$allowed = true;
				}
			}
		}

		return $allowed;
	}

	/**
	 * Empty Cache
	 *
	 * @param string $url
	 * @param string $user_agent
	 * @return string $robots
	 */
	public static function empty_cache()
	{
		$reset = false;

		if(defined('HUG_ROBOTS_CACHE_PATH'))
		{
			if(FileSystem::rrmdir(HUG_ROBOTS_CACHE_PATH))
			{
				$reset = true;
			}
		}

		return $reset;
	}
}