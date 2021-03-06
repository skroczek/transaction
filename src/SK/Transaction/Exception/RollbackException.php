<?php

/*
 * This file is part of the SK/Transaction package.
 *
 * (c) 2016 Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Exception;

use Exception;

/**
 * Class RollbackException.
 *
 * @author Sebastian Kroczek <sk@xbug.de>
 */
class RollbackException extends \RuntimeException
{
    /**
     * @var Exception
     */
    private $origin;

    public function __construct($message = '', $code = 0, Exception $previous = null, Exception $origin = null)
    {
        $this->origin = $origin;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get origin exception.
     *
     * @return Exception
     */
    public function getOrigin()
    {
        return $this->origin;
    }
}
