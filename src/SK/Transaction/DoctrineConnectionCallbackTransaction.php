<?php

/*
 * This file is part of the SK/Transaction package.
 *
 * (c) 2016 Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction;

use Doctrine\DBAL\Connection;

/**
 * Class DoctrineConnectionCallbackTransaction.
 *
 * @author Sebastian Kroczek <sk@xbug.de>
 */
class DoctrineConnectionCallbackTransaction extends AbstractDoctrineTransaction
{
    /**
     * @var callable
     */
    protected $execute;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     * @param callable   $execute
     */
    public function __construct(Connection $connection, \Closure $execute)
    {
        $this->execute = $execute;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(ParameterBag $parameterBag = null)
    {
        $callback = $this->execute;
        $callback($parameterBag, $this->getConnection());
    }
}
