# php-robots-txt

This librairy provides utilities function to ease robots.txt manipulation. If you want to check if URLs respect robots.txt policy with optional cache then you're ready to go.

[![Build Status](https://travis-ci.org/hugsbrugs/php-robots-txt.svg?branch=master)](https://travis-ci.org/hugsbrugs/php-robots-txt)
[![Coverage Status](https://coveralls.io/repos/github/hugsbrugs/php-robots-txt/badge.svg?branch=master)](https://coveralls.io/github/hugsbrugs/php-robots-txt?branch=master)

## Install

Install package with composer
```
composer require hugsbrugs/php-robots-txt
```

In your PHP code, load library
```php
require_once __DIR__ . '/../vendor/autoload.php';
use Hug\Robots\Robots as Robots;
```

## Usage

Returns if a page is accessible by respecting robots.txt policy
```php
Robots::is_allowed($url, $user_agent = null);
```
With this simple method a call to remote robots.txt will be fired on each request. To enable a cache define following variables
```php
define('HUG_ROBOTS_CACHE_PATH', '/path/to/robots-cache/');
define('HUG_ROBOTS_CACHE_DURATION', 7*86400);
```
Cache in seconds (86400: 1 day)

You Should not need following methods unless you want to play with code and tweak it !
```php
Robots::download_robots($url, $user_agent);
Robots::get_robots($url, $user_agent);
Robots::is_cache_obsolete($file);
Robots::is_allowed($url, $user_agent);
Robots::empty_cache();
```

## Unit Tests

```
composer exec phpunit
```

## Author

Hugo Maugey [visit my website ;)](https://hugo.maugey.fr)