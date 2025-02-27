# Async Process Manager

![GitHub Workflow Status on master Branch](https://github.com/agussuroyo/async/actions/workflows/test.yml/badge.svg?branch=master)


A lightweight and efficient PHP package to manage asynchronous processes with a configurable process limit.

## Installation
Install via Composer:
```
composer require agussuroyo/async
```


## Features
- Asynchronous process execution with `pcntl_fork`
- Configurable maximum number of parallel processes
- Automatic CPU core detection for optimal performance
- Efficient child process management

## Usage

### Basic Example
```php
use AgusSuroyo\Async\Async;

$async = new Async();

$async->run(function () {
    sleep(2);
    echo "Process 1 done\n";
});

$async->run(function () {
    sleep(3);
    echo "Process 2 done\n";
});

$async->wait();
```

### Setting Maximum Concurrent Processes
```php
$async = new Async(2); // Limit to 2 concurrent processes
```

### Dynamic Max Process Control
```php
$async = new Async();
$async->max(4); // Adjust max processes at runtime
```

## Testing
Run the tests using PHPUnit:
```sh
vendor/bin/phpunit
```

## License
This package is open-source and available under the MIT License.

