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
     * Set logger
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
     * Get logger. If not set, it returns an NullLogger instance
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
     * Append transaction
     *
     * @param TransactionInterface $transaction
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
     * Run the transaction
     *
     * @param ParameterBag $parameterBag
     *
     * @throws \Exception
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

    abstract protected function doExecute(ParameterBag $parameterBag = null);

    abstract protected function doRollback();
}