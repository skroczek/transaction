<?php
/*
 * This file is part of the transaction package.
 *
 * (c) Sebastian Kroczek <sebastian@kroczek.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Tests\Helper;


use SK\Transaction\AbstractDoctrineTransaction;
use SK\Transaction\ParameterBag;

abstract class AbstractDoctrineExceptionTransaction extends AbstractDoctrineTransaction
{
    /**
     * Execute the transaction.
     * If an error occurred, which prevent the transaction from fulfilling its propose, throw any exception you want,
     * to roll back the previous executed transactions.
     *
     * @param ParameterBag $parameterBag
     *
     * @throws \Exception
     */
    protected function doExecute(ParameterBag $parameterBag = null)
    {
        throw new \Exception('Dummy exception');
    }


}