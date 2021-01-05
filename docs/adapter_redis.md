# Redis/AMQP Adapter

## Requirements

```shell
sudo apt install php-redis
composer require php-amqplib/php-amqplib
```

## Using the adapter

### Configuration

| Key           | Default Value    | Desc
| ---           | ---              | ---
| host          | localhost        | Redis hostname
| port          | 5672             | Redis port
| user          | guest            | Redis username
| pass          | guest            | Redis password
| queue_name    | null             | Redis queue name

```php
$config = [
    'host' => 'localhost',
    'port' => 5672,
    'user' => 'guest',
    'pass' => 'guest',
    'queue_name' => 'my_queue_name',
];
$adapter = new \FmLabs\Q\Adapter\RabbitMqAdapter($config); 
```

## About the adapter


### Read more
- [Redis documentation](https://redis.io/documentation)


## Setup Redis server

https://redis.io/download

### From source code

Download, extract and compile Redis with:

```shell
$ wget https://download.redis.io/releases/redis-6.0.9.tar.gz
$ tar xzf redis-6.0.9.tar.gz
$ cd redis-6.0.9
$ make
```

The binaries that are now compiled are available in the src directory. Run Redis with:

```shell
$ src/redis-server
```

You can interact with Redis using the built-in client:

```shell
$ src/redis-cli
redis> set foo bar
OK
redis> get foo
"bar"
```

### From the official Ubuntu PPA

You can install the latest stable version of Redis from the redislabs/redis package repository. Add the repository to the apt index, update it and install:

```shell
$ sudo add-apt-repository ppa:redislabs/redis
$ sudo apt-get update
$ sudo apt-get install redis
```

### From Snapcraft

You can install the latest stable version of Redis from the Snapcraft marketplace:

```shell
$ sudo snap install redis
```