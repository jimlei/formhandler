<?php

namespace Jimlei\FormHandler\Tests;

use Jimlei\FormHandler\RequestInterface;

class TestRequest implements RequestInterface
{
    /**
     * @var array
     */
    private $data;

    public function __construct($data = array())
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}