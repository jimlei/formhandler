<?php

namespace Jimlei\FormHandler\Tests;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestEntity
     */
    private $entity;

    /**
     * @var TestForm
     */
    private $form;

    /**
     * @var TestRequest
     */
    private $request;

    public function setUp()
    {
        $this->request = new TestRequest();
        $this->entity = new TestEntity();
        $this->form = new TestForm($this->entity);
    }

    public function testGetErrorsBeforeRequest()
    {
        $this->assertEmpty($this->form->getErrors());
    }

    /**
     * @var array $data
     * @dataProvider validDataProvider
     */
    public function testGetErrorsWithValidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertEmpty($this->form->getErrors());
    }

    /**
     * @var array $data
     * @dataProvider invalidDataProvider
     */
    public function testGetErrorsWithInvalidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertNotEmpty($this->form->getErrors());
    }

    public function testIsValidBeforeRequest()
    {
        $this->assertTrue($this->form->isValid());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testIsValidWithValidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertTrue($this->form->isValid());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testIsValidWithInvalidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertFalse($this->form->isValid());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testHandleRequestWithValidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertTrue($this->form->isValid());
        $this->assertEquals($this->entity->getName(), $data['name']);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testHandleRequestWithInvalidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertFalse($this->form->isValid());
        $this->assertEquals($this->entity->getName(), null);
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testHandleRequestWithExistingEntityAndValidData($data)
    {
        $this->entity
            ->setName('Initial name');
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertTrue($this->form->isValid());
        $this->assertEquals($this->entity->getName(), $data['name']);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testHandleRequestWithExistingEntityAndInvalidData($data)
    {
        $initialName = 'Initial namn';
        $this->entity
            ->setId(1)
            ->setName($initialName);

        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertFalse($this->form->isValid());
        $this->assertEquals($this->entity->getName(), $initialName);
    }

    public function validDataProvider()
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

        return array(
            array(array('name' => 'Lorem ipsum'))
        );
    }

    public function invalidDataProvider()
    {
        return array(
            array(array()),
            array(array('name' => '')),
            array(array('name' => 'Lo')),
            array(array('name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit aene,'))
        );
    }
}