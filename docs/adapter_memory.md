# Memory Adapter

## Using the adapter

### Configuration

| Key           | Default Value    | Desc
| ---           | ---              | ---


```php
$adapter = new \FmLabs\Q\Adapter\MemoryAdapter(); 
```

## About the adapter

This adapter can only be used if the producer and the consume are using the same thread.

The queue length is limited by the memory available for the script.
You may want to adjust your php `memory_limit` setting.

### Read more:
- https://www.php.net/manual/en/ini.core.php#ini.memory-limit