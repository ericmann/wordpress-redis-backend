## Overview

A WordPress object cache backend that implements all available methods using the Redis PECL library.

## Authors

* Eric Mann
* Erick Hitter
* Tuan Duong

## Installation
1. Install and configure Redis. There is a good tutorial [here](http://www.saltwebsites.com/2012/install-redis-245-service-centos-6).
2. Install the [Redis PECL module](http://pecl.php.net/package/redis).
3. Add `object-cache.php` to the wp-content directory. It is a drop-in file, not a plugin, so it belongs in the wp-content directory, not the plugins directory.
4. By default, the script will connect to Redis at 127.0.0.1:6379. See the *Connecting to Redis* section for further options.

### Connecting to Redis ###

By default, the plugin uses `127.0.0.1` and `6379` as the default host and port when creating a new client instance; the default database of `0` is also used. Three constants are provided to override these default values.

Specify `WP_REDIS_BACKEND_HOST`, `WP_REDIS_BACKEND_PORT`, and `WP_REDIS_BACKEND_DB` to set the necessary, non-default connection values for your Redis instance.

### Prefixing Cache Keys ###

The constant `WP_CACHE_KEY_SALT` is provided to add a prefix to all cache keys used by the plugin. If running two single instances of WordPress from the same Redis instance, this constant could be used to avoid overlap in cache keys. Note that special handling is not needed for WordPress Multisite.

### In-memory cache support ###
It's disabled by default. Define `WP_INNER_CACHE` to enable this feature
```
define('WP_INNER_CACHE', true)
```
When this feature is enabled, cache data will be stored into PHP variable `$wp_inner_cache`, to avoid getting same redis key many times and save ton of requests to Redis
- Please ensure your PHP memory setting is enough for this feature or you have to write addition code to limit the `$wp_inner_cache` by `$group` and `$key`