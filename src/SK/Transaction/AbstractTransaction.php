<?php

/*
 * This file is part of the transaction package.
 *
 * (c) Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SK\Transaction\Exception\CircularReferenceException;
use SK\Transaction\Exception\RollbackException;

/**
 * Class AbstractTransaction.
 * You can use this class to implement your own transaction.
 *
 * @author  Sebastian Kroczek <sk@xbug.de>
 * @package SK\Transaction
 */
abstract class AbstractTransaction implements TransactionInterface
{
    /**
     * @var TransactionInterface
     */
    protected $child;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Set logger.
     *
     * @param LoggerInterface $logger
     *
     * @return AbstractTransaction
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        if (null !== $this->child) {
            $this->child->setLogger($logger);
        }

        return $this;
    }

    /**
     * Get logger. If not set it returns an NullLogger instance.
     *
     * @return LoggerInterface|NullLogger
     */
    protected function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function append(TransactionInterface $transaction)
    {
        if ($transaction === $this) {
            throw new CircularReferenceException();
        }
        if (null !== $this->child) {
            $this->child->append($transaction);
        } else {
            $this->child = $transaction;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ParameterBag $parameterBag = null)
    {
        try {
            $this->doExecute($parameterBag);
        } catch (\Exception $e) {
            $this->getLogger()->error(
                'An Exception Occurred during transaction: {exception_message}',
                array('exception_message' => $e->getMessage(), 'exception' => $e)
            );
            if ($this instanceof OwnExceptionRollback) {
                $this->rollback($e);
            }
            throw $e;
        }
        try {
            if (null !== $this->child) {
                $this->child->execute($parameterBag);
            }
        } catch (\Exception $e) {
            $this->rollback($e);
            throw $e;
        }

        if ($this instanceof Commit) {
            $this->commit();
        }
    }

    /**
     * Handle roll back of the transaction.
     * This method is only called if an exception occurred in one of the next transaction(s).
     * But you can change this behaviour by implementing the OwnExceptionRollback interface, In this case this method
     * is also called, if the exception occurred in the own execution method.
     * If an exception occurred during rollback this method throws an RollbackException exception.
     *
     * @param \Exception $e
     *
     * @throws RollbackException
     */
    protected function rollback(\Exception $e)
    {
        try {
            $this->doRollback();
        } catch (\Exception $re) {
            $this->getLogger()->critical(
                'An Exception Occurred during rollback: {exception_message}',
                array('exception_message' => $re->getMessage(), 'exception' => $re)
            );
            throw new RollbackException(
                sprintf('An error occurred during rollback: %s', $re->getMessage()), 0, $re, $e
            );
        }
    }

    /**
     * Execute the transaction.
     * If an error occurred, which prevent the transaction from fulfilling its propose, throw any exception you want,
     * to roll back the previous executed transactions.
     *
     * @param ParameterBag $parameterBag
     *
     * @throws \Exception
     */
    abstract protected function doExecute(ParameterBag $parameterBag = null);

    /**
     * Roll back the execution.
     * This method SHOULD NOT throw an exception.
     */
    abstract protected function doRollback();
}
