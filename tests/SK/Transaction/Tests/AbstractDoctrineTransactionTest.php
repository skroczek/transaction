<?php
/*
 * This file is part of the transaction package.
 *
 * (c) Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Tests;


use Doctrine\DBAL\Connection;


class AbstractDoctrineTransactionTest extends \PHPUnit_Framework_TestCase
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
     * @internal param int $transactionCount
     * @internal param bool $forceRollback
     *
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

    protected function createTransactionMock(Connection $connectionMock = null, $exception = false)
    {
        if (null === $connectionMock) {
            $connectionMock = $this->getConnectionMock();
        }

        if ($exception) {
            $class = 'SK\\Transaction\\Tests\\Helper\\AbstractDoctrineExceptionTransaction';
        } else {
            $class = 'SK\\Transaction\\AbstractDoctrineTransaction';
        }

        $transactionMock = $this->getMockBuilder($class)->getMockForAbstractClass();

        $transactionMock->expects($this->any())->method('getConnection')->willReturn($connectionMock);

        return $transactionMock;
    }

    public function testAddAction()
    {
        $connection = $this->getSuccessConnectionMock(2);

        $abstractTransaction = $this->createTransactionMock($connection);
        $transaction = $this->createTransactionMock($connection);
        $transaction
            ->expects($this->once())
            ->method('doExecute');


        $abstractTransaction->append($transaction);

        $abstractTransaction->execute();

    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Dummy exception
     */
    public function testRollbackTwoTransactions()
    {
        $connection = $this->getRollbackConnectionMock(2);

        $transaction1 = $this->createTransactionMock($connection, true);
//        $transaction1
//            ->expects($this->once())
//            ->method('doExecute')
//            ->willThrowException(new \Exception('Dummy Exception'));

        $transaction = $this->createTransactionMock($connection);
        $transaction->append($transaction1);

        $transaction->execute();
    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackThreeTransactionsSecondThrowsException()
    {
        $connection = $this->getRollbackConnectionMock(2);

        $transaction1 = $this->createTransactionMock($connection, true);
//        $transaction1
//            ->expects($this->once())
//            ->method('doExecute')
//            ->willThrowException(new \Exception('Dummy Exception'));


        $transaction2 = $this->createTransactionMock($connection);
        $transaction2
            ->expects($this->never())
            ->method('doExecute');

        $transaction = $this->createTransactionMock($connection);
        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackThreeTransactionsThirdThrowsException()
    {
        $connection = $this->getRollbackConnectionMock(3);
        $transaction = $this->createTransactionMock($connection);
        $transaction
            ->expects($this->once())
            ->method('doExecute');

        $transaction1 = $this->createTransactionMock($connection);
        $transaction1
            ->expects($this->once())
            ->method('doExecute');

        $transaction2 = $this->createTransactionMock($connection, true);
//        $transaction2
//            ->expects($this->once())
//            ->method('doExecute')
//            ->willThrowException(new \Exception('Dummy Exception'));

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    public function testMergeThreeTransactions()
    {
        $connection = $this->getSuccessConnectionMock(7);

        $transaction1 = $this->createTransactionMock($connection);
        $transaction11 = $this->createTransactionMock($connection);
        $transaction11
            ->expects($this->once())
            ->method('doExecute');

        $transaction12 = $this->createTransactionMock($connection);
        $transaction12
            ->expects($this->once())
            ->method('doExecute');

        $transaction1->append($transaction11);
        $transaction1->append($transaction12);

        $transaction2 = $this->createTransactionMock($connection);
        $transaction21 = $this->createTransactionMock($connection);
        $transaction21
            ->expects($this->once())
            ->method('doExecute');

        $transaction22 = $this->createTransactionMock($connection);
        $transaction22
            ->expects($this->once())
            ->method('doExecute');

        $transaction2->append($transaction21);
        $transaction2->append($transaction22);

        $transaction = $this->createTransactionMock($connection);

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    /**
     * @expectedException \Exception
     */
    public function testTwoConnectionsSecondThrowException()
    {
        $connection = $this->getRollbackConnectionMock(4, 4);

        $transaction1 = $this->createTransactionMock($connection);
        $transaction11 = $this->createTransactionMock($connection);
        $transaction11
            ->expects($this->once())
            ->method('doExecute');

        $transaction12 = $this->createTransactionMock($connection);
        $transaction12
            ->expects($this->once())
            ->method('doExecute');

        $transaction1->append($transaction11);
        $transaction1->append($transaction12);

        $connection2 = $this->getRollbackConnectionMock(3);
        $transaction2 = $this->createTransactionMock($connection2);
        $transaction21 = $this->createTransactionMock($connection2);
        $transaction21
            ->expects($this->once())
            ->method('doExecute');

        $transaction22 = $this->createTransactionMock($connection2, true);
//        $transaction22
//            ->expects($this->once())
//            ->method('doExecute')
//            ->willThrowException(new \Exception('Dummy Exception'));

        $transaction2->append($transaction21);
        $transaction2->append($transaction22);

        $transaction = $this->createTransactionMock($connection);

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }
}
