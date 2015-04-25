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

/**
 * Class AbstractDoctrineTransaction
 *
 * @author  Sebastian Kroczek <sk@xbug.de>
 * @package SK\Transaction
 */
abstract class AbstractDoctrineTransaction extends AbstractTransaction implements Commit, OwnExceptionRollback
{

    /**
     * @return Connection
     */
    abstract protected function getConnection();


    public function execute(ParameterBag $parameterBag = null)
    {
        $this->getConnection()->beginTransaction();

        parent::execute($parameterBag);
    }


    protected function doRollback()
    {
        $this->getConnection()->rollBack();
    }

    public function commit()
    {
        $this->getConnection()->commit();
    }
}