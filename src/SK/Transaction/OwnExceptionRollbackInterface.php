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
 * Transactions implementing this interface MUST call the own rollback method if an exception is thrown during
 * execution.
 *
 * @author Sebastian Kroczek <sk@xbug.de>
 */
interface OwnExceptionRollbackInterface
{
}
