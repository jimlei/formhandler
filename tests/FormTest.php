<?php

namespace Jimlei\FormHandler\Tests;

class FormTest extends \PHPUnit_Framework_TestCase
{
    private $entity;
    private $form;
    private $request;

    public function setUp()
    {
        $this->request = new Request();
        $this->entity = new TestEntity();
        $this->form = new TestForm($this->entity);
    }

    public function testGetErrorsBeforeRequest()
    {
        $this->assertEmpty($this->form->getErrors());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testGetErrorsWithValidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertEmpty($this->form->getErrors());
    }

    /**
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

        $this->assertEquals($this->entity->getTitle(), $data['title']);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testHandleRequestWithInvalidData($data)
    {
        $this->request->setData($data);
        $this->form->handleRequest($this->request);

        $this->assertEquals($this->entity->getTitle(), null);
    }

    public function validDataProvider()
    {
        return array(
            array(array('title' => 'a', 'text' => 'b')),
            array(array('title' => 'Lorem ipsum', 'text' => 'Curabitur congue eros turpis, non dapibus dolor molestie non. Integer arcu mauris, mattis non ullamcorper sed, viverra vitae tellus.')),
        );
    }

    public function invalidDataProvider()
    {
        return array(
            array(array('title' => '', '')),
            array(array('title' => 'Lorem ipsum curabitur congue eros turpis, non dapibus dolor molestie non.', 'text' => 'b')),
        );
    }
}