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


class AbstractTransactionTest extends \PHPUnit_Framework_TestCase
{

    protected function createTransactionMock()
    {
        return $this->getMockBuilder('SK\\Transaction\\AbstractTransaction')->getMockForAbstractClass();
    }

    public function testAddAction()
    {
        $abstractTransaction = $this->createTransactionMock();
        $transaction = $this->createTransactionMock();
        $transaction
            ->expects($this->once())
            ->method('doExecute');
        $transaction->expects($this->never())
            ->method('doRollback');

        $abstractTransaction->append($transaction);

        $abstractTransaction->execute();

    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackSingleTransaction()
    {
        $transaction = $this->createTransactionMock();
        $transaction
            ->expects($this->once())
            ->method('doExecute')
            ->willThrowException(new \Exception('Dummy Exception'));
        $transaction->expects($this->never())
            ->method('doRollback');


        $transaction->execute();
    }

    /**
     * @expectedException \SK\Transaction\Exception\CircularReferenceException
     */
    public function testCircularReferenceException()
    {
        $transaction = $this->createTransactionMock();
        $transaction->append($transaction);
    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackTwoTransactionsFirstThrowsException()
    {
        $transaction = $this->createTransactionMock();
        $transaction1 = $this->createTransactionMock();
        $transaction1
            ->expects($this->once())
            ->method('doExecute')
            ->willThrowException(new \Exception('Dummy Exception'));
        $transaction1->expects($this->never())
            ->method('doRollback');

        $transaction2 = $this->createTransactionMock();
        $transaction2
            ->expects($this->never())
            ->method('doExecute');
        $transaction2->expects($this->never())
            ->method('doRollback');

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackThreeTransactionsThirdThrowsException()
    {
        $transaction = $this->createTransactionMock();
        $transaction
            ->expects($this->once())
            ->method('doExecute');
        $transaction->expects($this->once())
            ->method('doRollback');

        $transaction1 = $this->createTransactionMock();
        $transaction1
            ->expects($this->once())
            ->method('doExecute');
        $transaction1->expects($this->once())
            ->method('doRollback');

        $transaction2 = $this->createTransactionMock();
        $transaction2
            ->expects($this->once())
            ->method('doExecute')
            ->willThrowException(new \Exception('Dummy Exception'));
        $transaction2->expects($this->never())
            ->method('doRollback');

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    public function testMergeThreeTransactions()
    {
        $transaction1 = $this->createTransactionMock();
        $transaction11 = $this->createTransactionMock();
        $transaction11
            ->expects($this->once())
            ->method('doExecute');
        $transaction11->expects($this->never())
            ->method('doRollback');

        $transaction12 = $this->createTransactionMock();
        $transaction12
            ->expects($this->once())
            ->method('doExecute');
        $transaction12->expects($this->never())
            ->method('doRollback');

        $transaction1->append($transaction11);
        $transaction1->append($transaction12);

        $transaction2 = $this->createTransactionMock();
        $transaction21 = $this->createTransactionMock();
        $transaction21
            ->expects($this->once())
            ->method('doExecute');
        $transaction21->expects($this->never())
            ->method('doRollback');

        $transaction22 = $this->createTransactionMock();
        $transaction22
            ->expects($this->once())
            ->method('doExecute');
        $transaction22->expects($this->never())
            ->method('doRollback');

        $transaction2->append($transaction21);
        $transaction2->append($transaction22);

        $transaction = $this->createTransactionMock();

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $transaction->execute();
    }

    public function testRollbackExceptionTwoTransactions()
    {


        $transaction = $this->createTransactionMock();
        $transaction1 = $this->createTransactionMock();
        $transaction1
            ->expects($this->once())
            ->method('doExecute');
        $transaction1->expects($this->once())
            ->method('doRollback')
            ->willThrowException(new \Exception('Dummy Exception two'));

        $transaction2 = $this->createTransactionMock();
        $transaction2
            ->expects($this->once())
            ->method('doExecute')
            ->willThrowException(new \Exception('Dummy Exception'));
        $transaction2->expects($this->never())
            ->method('doRollback');

        $transaction->append($transaction1);
        $transaction->append($transaction2);

        $logger = $this->getMock('\Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('error');
        $logger->expects($this->once())->method('emergency');

        $transaction->setLogger($logger);

        try {
            $transaction->execute();
        } catch (\SK\Transaction\Exception\RollbackException $e) {
            $this->assertEquals('An exception occurred during rollback: Dummy Exception two', $e->getMessage());
            $this->assertInstanceOf('\Exception', $e->getPrevious());
            $this->assertEquals('Dummy Exception two', $e->getPrevious()->getMessage());
            $this->assertInstanceOf('\Exception', $e->getOrigin());
            $this->assertEquals('Dummy Exception', $e->getOrigin()->getMessage());
        }
    }

    public function testLogger()
    {

        $transaction = $this->createTransactionMock();
        $transaction1 = $this->createTransactionMock();
        $transaction1
            ->expects($this->once())
            ->method('doExecute');
        $transaction1->expects($this->once())
            ->method('doRollback')
            ->willThrowException(new \Exception('Dummy Exception two'));

        $transaction2 = $this->createTransactionMock();
        $transaction2
            ->expects($this->once())
            ->method('doExecute')
            ->willThrowException(new \Exception('Dummy Exception'));
        $transaction2->expects($this->never())
            ->method('doRollback');

        $logger = $this->getMock('\Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('error');
        $logger->expects($this->once())->method('emergency');

        $transaction->setLogger($logger);

        $transaction->append($transaction1);
        $transaction->append($transaction2);



        try {
            $transaction->execute();
        } catch (\SK\Transaction\Exception\RollbackException $e) {
            $this->assertEquals('An exception occurred during rollback: Dummy Exception two', $e->getMessage());
            $this->assertInstanceOf('\Exception', $e->getPrevious());
            $this->assertEquals('Dummy Exception two', $e->getPrevious()->getMessage());
            $this->assertInstanceOf('\Exception', $e->getOrigin());
            $this->assertEquals('Dummy Exception', $e->getOrigin()->getMessage());
        }
    }
}
