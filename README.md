# php-q

Simple queue abstraction layer for PHP.

[![Build Status](https://travis-ci.org/fm-labs/php-q.svg?branch=main)](https://travis-ci.org/fm-labs/php-q)

## Requirements

- php 7.2+

## Installation

```console
$ composer require fm-labs/php-q
```

## Usage

```php
use FmLabs\Q\Q;
use FmLabs\Q\Queue\BaseQueue;
use FmLabs\Q\Adapter\MemoryAdapter;
use FmLabs\Q\Message\TextMessage;

// Configure a queue
Q::config('awesome_queue');
// (same as)
Q::config('awesome_queue', [
    'queueClass' => \FmLabs\Q\Queue\BaseQueue::class,
    'adapterClass' => \FmLabs\Q\Adapter\MemoryAdapter::class,
    'messageClass' => \FmLabs\Q\Message\TextMessage::class,
]);


// Push a message to the queue
Q::push('awesome_queue', new TextMessage('Hello world!'));


// Pop message from queue
$msg = Q::pop('awesome_queue');
echo $msg->getPayload(); // 'Hello World!'
```

## Advanced usage
```php
use FmLabs\Q\Q;

// Rejecting a message
$msg = Q::pop('awesome_queue');
if ($msg->getPayload() != 'Hello World!') {
    //$msg->reject();
    Q::reject('awesome_queue', $msg);
}


// Requeue a message
$msg = Q::pop('awesome_queue');
if ($msg->getPayload() != 'Hello World!') {
    //$msg->requeue();
    Q::requeue('awesome_queue', $msg);
}


// Drop a message
$msg = Q::pop('awesome_queue');
if ($msg->getPayload() != 'Hello World!') {
    //$msg->drop();
    Q::drop('awesome_queue', $msg);
}
```


## Docs

See [documentation](docs/index.md)


## Run tests
```console
$ composer run test
$ composer run test-verbose
$ ./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
```

## Changelog
[0.1]
- Added Queue-, Adapter-, Message-Interfaces
- Added MemoryAdapter
- Added RabbitMqAdapter


## Roadmap
- LocalFile Adapter
- Redis Adapter
- Amazon AWS SQS Adapter
- PDO Adapter
- Memcached Adapter
- MongoDB Adapter
- Beanstalkd Adapter
- Http Adapter
- IronMQ Adapter  
- Stomp Adapter
- WindowsAzure ServiceBus Adapter

## License

See [LICENSE](LICENSE) file



