<?php

# For PHP7
// declare(strict_types=1);

// namespace Hug\Tests\Robots;

use PHPUnit\Framework\TestCase;

use Hug\Robots\Robots as Robots;

/**
 *
 */
final class RobotsTest extends TestCase
{    

	public $url_allowed = 'https://hugo.maugey.fr';
	public $url_no_allowed = 'https://hugo.maugey.fr/php/coucou.html';
	public $user_agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0';


	function __construct()
	{
		$data = realpath(__DIR__ . '/../../../data/');
		
		// define('HUG_ROBOTS_CACHE_PATH', $data . '/');
		// define('HUG_ROBOTS_CACHE_DURATION', 86400);

		// $this->file = HUG_ROBOTS_CACHE_PATH . '/hugo.maugey.fr.txt';
		$this->file = $data . '/hugo.maugey.fr.txt';
	}

    /* ************************************************* */
    /* ************ Robots::download_robots ************ */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownloadRobots()
    {
        $test = Robots::download_robots($this->url_allowed, $this->user_agent);
        $this->assertInternalType('string', $test);
    }

    /* ************************************************* */
    /* *************** Robots::get_robots ************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetRobots()
    {
        $test = Robots::get_robots($this->url_allowed, $this->user_agent);
        $this->assertInternalType('string', $test);
    }

    /* ************************************************* */
    /* ************ Robots::is_cache_obsolete ********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsCacheObsolete()
    {
    	define('HUG_ROBOTS_CACHE_DURATION', 86400);
        $test = Robots::is_cache_obsolete($this->file);
        $this->assertFalse($test);
    }


    /* ************************************************* */
    /* *************** Robots::is_allowed ************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsAllowed()
    {
        $test = Robots::is_allowed($this->url_allowed, $this->user_agent);
        $this->assertTrue($test);
    }

    /**
     *
     */
    public function testCannotIsAllowed()
    {
        $test = Robots::is_allowed($this->url_no_allowed, $this->user_agent);
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* *************** Robots::empty_cache ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanEmptyCache()
    {
    	$data = realpath(__DIR__ . '/../../../data/');
		define('HUG_ROBOTS_CACHE_PATH', $data . '/');
        $test = Robots::empty_cache();
        $this->assertTrue($test);
    }

}
