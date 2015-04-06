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

    public function tearDown()
    {
        unset($this->form);
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
        return array(
            array(array('name' => 'Lorem ipsum')),
            array(array('name' => 'Lorem ipsum', 'email' => 'test@local.com', 'homepage' => 'https://github.com/jimlei/formhandler', 'cylinders' => 8, 'power' => 525.03, 'productionStart' => 1399413600, 'active' => 1, 'createdBy' => '127.0.0.1')),
            array(array('name' => 'Lorem ipsum', 'email' => 'test@local.com', 'homepage' => 'https://github.com/jimlei/formhandler', 'cylinders' => '8', 'power' => '525', 'productionStart' => '1399413600', 'active' => '1', 'createdBy' => '69.50.225.155')),
            array(array('name' => 'Lorem ipsum', 'email' => 'test@local.com', 'homepage' => 'https://github.com/jimlei/formhandler', 'cylinders' => '8', 'power' => '525.03', 'productionStart' => '1399413600', 'active' => '1', 'createdBy' => '192.237.215.35'))
        );
    }

    public function invalidDataProvider()
    {
        return array(
            array(array()),
            array(array('name' => '')),
            array(array('name' => 'Lo')),
            array(array('name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit aene,')),
            array(array('name' => 'Lorem ipsum', 'email' => '')),
            array(array('name' => 'Lorem ipsum', 'email' => 'invalidEmail')),
            array(array('name' => 'Lorem ipsum', 'homepage' => 'invalidUrl')),
            array(array('name' => 'Lorem ipsum', 'cylinders' => 'invalidCylinders')),
            array(array('name' => 'Lorem ipsum', 'cylinders' => 0)),
            array(array('name' => 'Lorem ipsum', 'cylinders' => 100)),
            array(array('name' => 'Lorem ipsum', 'power' => 'invalidPower')),
            array(array('name' => 'Lorem ipsum', 'power' => -1)),
            array(array('name' => 'Lorem ipsum', 'productionStart' => 'invalidProductionStart')),
            array(array('name' => 'Lorem ipsum', 'active' => 'invalidActive')),
            array(array('name' => 'Lorem ipsum', 'createdBy' => 'invalidIp')),
            array(array('name' => 'Lorem ipsum', 'createdBy' => 0)),
        );
    }
}