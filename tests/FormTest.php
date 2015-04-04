<?php

use Jimlei\FormHandler\Form;

class FormTest extends PHPUnit_Framework_TestCase
{
    public function testCanBeNegated()
    {
        $a = 1;

        $this->assertEquals(1, $a);
    }
}