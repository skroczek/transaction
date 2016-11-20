# Transaction
[![Build Status](https://travis-ci.org/skroczek/transaction.svg?branch=master)](https://travis-ci.org/skroczek/transaction) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/26252d9a-f7c7-4ece-8a83-40b40149272c/mini.png)](https://insight.sensiolabs.com/projects/26252d9a-f7c7-4ece-8a83-40b40149272c) [![Coverage Status](https://coveralls.io/repos/github/skroczek/transaction/badge.svg?branch=master)](https://coveralls.io/github/skroczek/transaction?branch=master)

## Introduction

This library claims to provide an easy way to implement secure transactions during nearly everything.

## Installation

This library can be easily installed via composer

    composer require sk/transaction

or just add it to your composer.json file directly.

## Usage

As a basic usage example, the CallbackTransaction is used to demonstrate the behaviour during three api calls:

```php

<?php
use SK\Transaction\CallbackTransaction;
use SK\Transaction\Exception\RollbackException;
use SK\Transaction\ParameterBag;
use Acme\Api1Client;
use Acme\Api2Client;
use Acme\Api3Client;

$api1Client = new Api1Client();
$callbackTransaction = new CallbackTransaction(
    // Do something important
    function (ParameterBag $parameters) use ($api1Client) {
        $api1Client->doSomethingImportant($parameters);
    },
    // Roll back if an exception in one of the next transaction(s) occurred.
    // For more information see interface \SK\Transaction\OwnExceptionRollback
    function () use ($api1Client) {
        $api1Client->rollback();
    }
);

$api2Client = new Api2Client();
$callbackTransaction2 = new CallbackTransaction(
    function (ParameterBag $parameters = null) use ($api2Client) {
        $api2Client->doSomethingImportant($parameters);
    },
    function () use ($api2Client) {
        $api2Client->rollback();
    }
);

$api3Client = new Api3Client();
$callbackTransaction2 = new CallbackTransaction(
    function (ParameterBag $parameters) use ($api3Client) {
        $api3Client->doSomethingImportant($parameters);
    },
    function () use ($api3Client) {
        // This will never executed. For more information see \SK\Transaction\OwnExceptionRollback
        $api3Client->rollback();
    }
);

$callbackTransaction->append($callbackTransaction2);
$callbackTransaction->append($callbackTransaction3);
// or
// $callbackTransaction2->append($callbackTransaction3);

try {
    $callbackTransaction->execute();
} catch (RollbackException $e) {
    // Something really bad happens
    // But you can get the Exception which causes the rollback
    $e->getOrigin();
    // And you can get the exception which occurred during rollback
    $e->getPrevious();
} catch (\Exception $e) {
    // An exception occurred, but all executed actions was rolled back successfully
}
```

## ToDo

* Write more documentation
* Fix typos
        
## License

This library is under the MIT license. See the complete license in the LICENCE file.
