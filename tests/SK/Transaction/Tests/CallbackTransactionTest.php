<?php

/*
 * This file is part of the SK/Transaction package.
 *
 * (c) 2016 Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Tests;

use SK\Transaction\CallbackTransaction;

class CallbackTransactionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteCallbackIsCalled()
    {
        $executeExecuted = false;
        $execute = function () use (&$executeExecuted) {
            $executeExecuted = true;
        };

        $rollbackExecuted = false;
        $rollback = function () use (&$rollbackExecuted) {
            $rollbackExecuted = true;
        };

        $callbackTransaction = new CallbackTransaction($execute, $rollback);

        $callbackTransaction->execute();

        $this->assertTrue($executeExecuted);
        $this->assertFalse($rollbackExecuted);
    }

    public function testRollbackCallbackIsCalled()
    {
        $executeExecuted1 = false;
        $execute = function () use (&$executeExecuted1) {
            $executeExecuted1 = true;
        };

        $rollbackExecuted1 = false;
        $rollback = function () use (&$rollbackExecuted1) {
            $rollbackExecuted1 = true;
        };

        $callbackTransaction = new CallbackTransaction($execute, $rollback);

        $executeExecuted2 = false;
        $execute2 = function () use (&$executeExecuted2) {
            $executeExecuted2 = true;
            throw new \Exception('Dummy Exception');
        };

        $rollbackExecuted2 = false;
        $rollback2 = function () use (&$rollbackExecuted2) {
            $rollbackExecuted2 = true;
        };

        $callbackTransaction2 = new CallbackTransaction($execute2, $rollback2);

        $callbackTransaction->append($callbackTransaction2);

        $exceptionCaught = false;
        try {
            $callbackTransaction->execute();
        } catch (\Exception $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($executeExecuted1);
        $this->assertTrue($rollbackExecuted1);
        $this->assertTrue($executeExecuted2);
        $this->assertFalse($rollbackExecuted2);
        $this->assertTrue($exceptionCaught);
    }

    public function testCommitCallbackIsCalled()
    {
        $executeExecuted = false;
        $execute = function () use (&$executeExecuted) {
            $executeExecuted = true;
        };

        $rollbackExecuted = false;
        $rollback = function () use (&$rollbackExecuted) {
            $rollbackExecuted = true;
        };

        $commitExecuted = false;
        $commit = function () use (&$commitExecuted) {
            $commitExecuted = true;
        };

        $callbackTransaction = new CallbackTransaction($execute, $rollback, $commit);

        $callbackTransaction->execute();

        $this->assertTrue($executeExecuted);
        $this->assertFalse($rollbackExecuted);
        $this->assertTrue($commitExecuted);
    }
}
