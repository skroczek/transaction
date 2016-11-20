<?php

/*
 * This file is part of the transaction package.
 *
 * (c) Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Exception;

/**
 * Class CircularReferenceException.
 *
 * @author Sebastian Kroczek <sk@xbug.de>
 */
class CircularReferenceException extends \RuntimeException
{
    public function __construct($message = 'A circular reference has been detected.', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
