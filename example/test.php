<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hug\Robots\Robots as Robots;

# Let webmasters know who you are
// ini_set('user_agent','YourBotName/Version (http://www.yourwebsite.com/whatyourbotdoes.php)');

$url = 'https://hugo.maugey.fr';
$url = 'https://hugo.maugey.fr/php/coucou.html';
$user_agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0';

// $is_allowed = Robots::is_allowed($url, $user_agent);
// var_dump($is_allowed);

define('HUG_ROBOTS_CACHE_PATH', '/var/www/php-utils/php-robots-txt/data/');
define('HUG_ROBOTS_CACHE_DURATION', 86400);

$is_allowed = Robots::is_allowed($url, $user_agent);
var_dump($is_allowed);

$url = 'https://hugo.maugey.fr/coco/coucou.html';

$is_allowed = Robots::is_allowed($url, $user_agent);
var_dump($is_allowed);

$empty_cache = Robots::empty_cache();
var_dump($empty_cache);