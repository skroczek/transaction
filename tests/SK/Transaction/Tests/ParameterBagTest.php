<?php

/*
 * This file is part of the SK/Transaction package.
 *
 * (c) 2016 Sebastian Kroczek <sk@xbug.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SK\Transaction\Tests;

use SK\Transaction\ParameterBag;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function testOffsetSetAndExists()
    {
        $pb = new ParameterBag();

        $this->assertFalse($pb->offsetExists('foobar'));
        $this->assertFalse(isset($pb['foobar']));

        $pb->offsetSet('foobar', '');

        $this->assertTrue($pb->offsetExists('foobar'));
        $this->assertTrue(isset($pb['foobar']));

        $pb['barfoo'] = '';

        $this->assertTrue($pb->offsetExists('barfoo'));
        $this->assertTrue(isset($pb['barfoo']));
    }

    public function testOffsetGet()
    {
        $pb = new ParameterBag();

        $this->assertNull($pb->offsetGet('foobar'));
        $this->assertNull($pb['foobar']);

        $pb->offsetSet('foobar', '');

        $this->assertEmpty($pb->offsetGet('foobar'));
        $this->assertEmpty($pb['foobar']);

        $pb->offsetSet('foobar', true);

        $this->assertTrue($pb->offsetGet('foobar'));
        $this->assertTrue($pb['foobar']);
    }

    public function testOffsetUnset()
    {
        $pb = new ParameterBag();

        $pb->offsetSet('foobar', true);

        $pb->offsetUnset('foobar');

        $this->assertNull($pb->offsetGet('foobar'));
        $this->assertNull($pb['foobar']);
    }

    public function testOffsetUnsetUnknownOffset()
    {
        $pb = new ParameterBag();

        $pb->offsetUnset('foobar');

        $this->assertNull($pb->offsetGet('foobar'));
        $this->assertNull($pb['foobar']);
    }
}
