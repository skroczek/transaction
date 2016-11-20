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

use SK\Transaction\DoctrineConnectionCallbackTransaction;

class DoctrineCallbackTransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrine\DBAL\Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getConnectionMock()
    {
        $mock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'beginTransaction',
                    'commit',
                    'rollback',
                    'prepare',
                    'query',
                    'executeQuery',
                    'executeUpdate',
                    'getDatabasePlatform',
                )
            )
            ->getMock();

        return $mock;
    }

    /**
     * @return \Doctrine\DBAL\Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getSuccessConnectionMock($transactionCount = 1)
    {
        $connection = $this->getConnectionMock();
        $connection->expects($this->exactly($transactionCount))
            ->method('beginTransaction');
        $connection->expects($this->exactly($transactionCount))
            ->method('commit');
        $connection->expects($this->never())
            ->method('rollback');

        return $connection;
    }

    /**
     * Create connection mock for doRollback transaction.
     *
     * @param int $transactionBeginCount
     * @param int $rollbackCount
     *
     * @return Connection|\PHPUnit_Framework_MockObject_MockObject
     *
     * @internal param int $transactionCount
     * @internal param bool $forceRollback
     */
    public function getRollbackConnectionMock($transactionBeginCount = 1, $rollbackCount = null)
    {
        if (null === $rollbackCount) {
            $rollbackCount = $transactionBeginCount;
        }

        $connection = $this->getConnectionMock();
        $connection->expects($this->exactly($transactionBeginCount))
            ->method('beginTransaction');
        $connection->expects($this->never())
            ->method('commit');

        $connection->expects($this->exactly($rollbackCount))
            ->method('rollback');

        return $connection;
    }

    public function testExecuteCallbackIsCalled()
    {
        $executeExecuted = false;
        $execute = function () use (&$executeExecuted) {
            $executeExecuted = true;
        };

        $callbackTransaction = new DoctrineConnectionCallbackTransaction($this->getSuccessConnectionMock(), $execute);

        $callbackTransaction->execute();

        $this->assertTrue($executeExecuted);
    }
}
