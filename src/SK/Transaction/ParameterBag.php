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

class ParameterBag implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $bag;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bag = array();
    }


    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->bag[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->bag[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value = null)
    {
        $this->bag[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->bag[$offset]);
    }
}
