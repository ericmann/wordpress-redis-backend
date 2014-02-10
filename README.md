## Overview

A WordPress object cache backend that implements all available methods using Redis and the Predis library for PHP.

## Authors

* Eric Mann

## Installation
1. Install and configure Redis. There is a good tutorial [here](http://www.saltwebsites.com/2012/install-redis-245-service-centos-6).
2. Install the plugin by placing into the plugins directory and activating via the WP Admin Dashboard.
3. By default, the script will connect to Redis at 127.0.0.1:6379.

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
