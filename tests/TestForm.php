<?php

namespace Jimlei\FormHandler\Tests;

use Jimlei\FormHandler\Form;

class TestForm extends Form
{
    public function __construct(TestEntity $entity)
    {
        $fields = array(
            'title' => array(
                'type' => 'string',
                'maxLength' => '60',
                'required' => true
            ),
            'text' => array(
                'type' => 'string',
                'maxLength' => '5000',
                'required' => true
            ),
            'publishDate' => array(
                'type' => 'datetime',
            )
        );

        parent::__construct($entity, $fields);
    }
}