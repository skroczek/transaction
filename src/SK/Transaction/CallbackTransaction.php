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
    /**
     * @var callable
     */
    protected $execute;

    /**
     * @var callable
     */
    protected $rollback;

    /**
     * @var callable
     */
    protected $commit;

    /**
     * Constructor
     *
     * @param callable|\Closure $execute  Callable or closure to execute
     * @param callable|\Closure $rollback Callable or closure which manage the rollback
     * @param callable|\Closure $commit   Optional commit callable or closure
     */
    public function __construct(\Closure $execute, \Closure $rollback, \Closure $commit = null)
    {
        $this->execute = $execute;
        $this->rollback = $rollback;
        $this->commit = $commit;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(ParameterBag $parameterBag = null)
    {
        $callback = $this->execute;
        $callback($parameterBag);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRollback()
    {
        $callback = $this->rollback;
        $callback();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        if (null !== $this->commit) {
            $callback = $this->commit;
            $callback();
        }
    }
}
