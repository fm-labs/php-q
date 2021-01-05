# RabbitMQ/AMQP Adapter

## Requirements

```shell
composer require php-amqplib/php-amqplib
```

## Using the adapter

### Configuration

| Key           | Default Value    | Desc
| ---           | ---              | ---
| host          | localhost        | RabbitMQ hostname
| port          | 5672             | RabbitMQ port
| user          | guest            | RabbitMQ username
| pass          | guest            | RabbitMQ password
| queue_name    | null             | RabbitMQ queue name

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

Uses AMQP Pull API for `QueueAdapterInterface::pop` operations
(which is not the most efficient way to use an AMQP queue, but works in this context).

Messages are acknowledged manually.

### Read more
- [RabbitMQ: Queues](https://www.rabbitmq.com/queues.html)
- [RabbitMQ: Fetching Individual Messages ("Pull API")](https://www.rabbitmq.com/consumers.html#fetching)
- [RabbitMQ: Consumer Acknowledgements and Publisher Confirms](https://www.rabbitmq.com/confirms.html)
- [RabbitMQ: Define Message TTL for Queues Using x-arguments During Declaration](https://www.rabbitmq.com/ttl.html#message-ttl-using-x-args)
- [RabbitMQ: Parameters and Policies](https://www.rabbitmq.com/parameters.html)
