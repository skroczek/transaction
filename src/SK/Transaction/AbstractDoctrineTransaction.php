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
 * Class AbstractDoctrineTransaction.
 *
 * @author  Sebastian Kroczek <sk@xbug.de>
 */
abstract class AbstractDoctrineTransaction extends AbstractTransaction implements CommitInterface, OwnExceptionRollbackInterface
{
    /**
     * @return Connection
     */
    abstract protected function getConnection();

    /**
     * {@inheritdoc}
     */
    public function execute(ParameterBag $parameterBag = null)
    {
        $this->getConnection()->beginTransaction();

        parent::execute($parameterBag);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRollback()
    {
        $this->getConnection()->rollBack();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->getConnection()->commit();
    }
}
