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
            'email' => array(
                'type' => 'email'
            ),
            'homepage' => array(
                'type' => 'url'
            ),
            'cylinders' => array(
                'type' => 'int',
                'min' => 1,
                'max' => 16
            ),
            'power' => array(
                'type' => 'float',
                'min' => 0
            ),
            'productionStart' => array(
                'type' => 'timestamp'
            ),
            'active' => array(
                'type' => 'bool'
            ),
            'createdBy' => array(
                'type' => 'ip'
            )
        );

        parent::__construct($entity, $fields);
    }
}