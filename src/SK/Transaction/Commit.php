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

/**
 * Interface Commit. Transactions implementing this interface MUST call the commit method after the successful
 * execution of all other transactions.
 *
 * @package SK\Transaction
 * @author  Sebastian Kroczek <sk@xbug.de>
 */
interface Commit
{
    /**
     * Commit the transaction. This method MUST NOT throw an exception.
     *
     * @return void
     */
    public function commit();
}