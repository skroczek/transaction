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
 * Transactions which implements this interface will call the own doRollback method if an exception is thrown during
 * execution.
 *
 * @package SK\Transaction
 */
interface OwnExceptionRollback
{

}