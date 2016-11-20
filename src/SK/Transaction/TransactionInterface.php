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

/**
 * Interface TransactionInterface.
 *
 * @author Sebastian Kroczek <sk@xbug.de>
 */
interface TransactionInterface
{
    /**
     * Append another transaction.
     *
     * @param TransactionInterface $transaction
     *
     * @return mixed
     */
    public function append(TransactionInterface $transaction);

    /**
     * Execute the transaction. This method MUST only be successful if all appended transactions also finished
     * successful. It MUST be guaranteed that an unsuccessful execution rolls back everything.
     *
     * @param ParameterBag $parameterBag
     *
     * @return mixed
     */
    public function execute(ParameterBag $parameterBag = null);
}
