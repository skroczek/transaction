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


use Doctrine\DBAL\Connection;

class DoctrineConnectionCallbackTransaction extends AbstractDoctrineTransaction
{

    /**
     * @var \Closure
     */
    protected $execute;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection, \Closure $execute)
    {
        $this->execute = $execute;
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    protected function doExecute(ParameterBag $parameterBag = null)
    {
        $callback = $this->execute;
        $callback($parameterBag, $this->getConnection());
    }


}