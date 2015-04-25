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


class CallbackTransaction extends AbstractTransaction implements Commit
{

    protected $execute;

    protected $rollback;

    protected $commit;

    public function __construct(\Closure $execute, \Closure $rollback, \Closure $commit = null)
    {
        $this->execute = $execute;
        $this->rollback = $rollback;
        $this->commit = $commit;
    }

    protected function doExecute(ParameterBag $parameterBag = null)
    {
        $callback = $this->execute;
        $callback($parameterBag);
    }

    protected function doRollback()
    {
        $callback = $this->rollback;
        $callback();
    }

    public function commit()
    {
        if (null !== $this->commit) {
            $callback = $this->commit;
            $callback();
        }
    }
}