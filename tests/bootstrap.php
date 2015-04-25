<?php
/*
 * This file is part of the transaction library.
 *
 * (c) Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

call_user_func(
    function () {
        if (!is_file(__DIR__ . '/../vendor/autoload.php')) {
            throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
        }
        $loader = require __DIR__ . '/../vendor/autoload.php';

//        $loader->add('SK\Transaction\Tests', __DIR__ . '/SK/Transaction/Tests');
    }
);