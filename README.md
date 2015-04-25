# Transaction

## Introduction

## Installation

This library can be easily installed via composer

    composer require sk/transaction

or just add it to your composer.json file directly.

## Usage

```php
<?php

use SK\Transaction\CallbackTransaction;
use SK\Transaction\Exception\RollbackException;
use SK\Transaction\ParameterBag;
use Acme\Api1Client;
use Acme\Api2Client;

$api1Client = new Api1Client();

$callbackTransaction = new CallbackTransaction(
    // Do something important
    function (ParameterBag $parameters) use ($api1Client) {
        $api1Client->doSomethingImportant($parameters);
    },
    // Roll back if an exception in one of the next transaction(s) occurred.
    // For more information see interface \SK\Transaction\OwnExceptionRollback
    function () use ($api1Client) {
        $api1Client->rollbackSomethingImportant();
    }
);

$api2Client = new Api2Client();
$callbackTransaction2 = new CallbackTransaction(
    function (ParameterBag $parameters) use ($api2Client) {
        $api2Client->doAlsoSomethingImportant($parameters);
    },
    function () use ($api2Client) {
        $api2Client->rollbackAlsoSomethingImportant();
    }
);

$callbackTransaction->append($callbackTransaction2);

try {
    $callbackTransaction->execute();
}catch(RollbackException $e){
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
        
## License

This library is under the MIT license. See the complete license in the LICENCE file.