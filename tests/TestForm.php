<?php

namespace Jimlei\FormHandler\Tests;

use Jimlei\FormHandler\Form;

class TestForm extends Form
{
    public function __construct(TestEntity $entity)
    {
        $fields = array(
            'name' => array(
                'type' => 'string',
                'minLength' => 3,
                'maxLength' => 60,
                'required' => true
            ),
            'cylinders' => array(
                'type' => 'int',
                'min' => 1
            ),
            'power' => array(
                'type' => 'float',
                'min' => 0
            ),
            'productionStart' => array(
                'type' => 'timestamp'
            )
        );

        parent::__construct($entity, $fields);
    }
}