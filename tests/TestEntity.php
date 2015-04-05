<?php

namespace Jimlei\FormHandler\Tests;

class TestEntity
{
    private $id;
    private $name;
    private $email;
    private $cylinders;
    private $power;
    private $productionStart;
    private $active;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getCylinders()
    {
        return $this->cylinders;
    }

    public function setCylinders($cylinders)
    {
        $this->cylinders = $cylinders;
        return $this;
    }

    public function getPower()
    {
        return $this->power;
    }

    public function setPower($power)
    {
        $this->power = $power;
        return $this;
    }

    public function getProductionStart()
    {
        return $this->productionStart;
    }

    public function setProductionStart($productionStart)
    {
        $this->productionStart = $productionStart;
        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }
}