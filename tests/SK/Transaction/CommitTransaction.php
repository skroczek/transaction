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
 * Dummy class for testing commit interface
 *
 * @author  Sebastian Kroczek <sk@xbug.de>
 * @package SK\Transaction
 */
class CommitTransaction extends AbstractTransaction implements Commit {

    protected function doExecute()
    {
        // Do Nothing
    }

    protected function doRollback()
    {
        // Do Nothing
    }

    public function commit()
    {
        // Do Nothing
    }
}