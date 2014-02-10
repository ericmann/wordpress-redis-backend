## Overview

A WordPress object cache backend that implements all available methods using Redis and the Predis library for PHP.

## Authors

* Eric Mann

## Installation
### Composer
1. Install and configure Redis. There is a good tutorial [here](http://www.saltwebsites.com/2012/install-redis-245-service-centos-6).
2) Include the following in your root composer.json
```js
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ericmann/wordpress-redis-backend.git"
        }
    ],
    "require": {
        "ericmann/wordpress-redis-backend": "dev-master"
    }
}
```

( I recommend using Roots.io's [Bedrock](https://github.com/roots/bedrock) for site organization )

### Manually
1. Install and configure Redis. There is a good tutorial [here](http://www.saltwebsites.com/2012/install-redis-245-service-centos-6).
2. Make sure Composer is installed. If it's not, [here's how to install](https://getcomposer.org/doc/00-intro.md#installation-nix) ( You'll need commandline access to your server )
3. Install the plugin by placing into the plugins directory and then run `composer install` in the root of the plugin directory.
4. Activate WordPress Redis Backend in the WordPress admin
5. By default, the script will connect to Redis at 127.0.0.1:6379.

### Connecting to Redis ###

By default Predis uses `127.0.0.1` and `6379` as the default host and port when creating a new client
instance without specifying any connection parameter:

```php
$redis = new Predis\Client();
$redis->set('foo', 'bar');
$value = $redis->get('foo');
```

It is possible to specify the various connection parameters using URI strings or named arrays:

```php
$redis = new Predis\Client('tcp://10.0.0.1:6379');

// is equivalent to:

$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => '10.0.0.1',
    'port'   => 6379,
));
```
