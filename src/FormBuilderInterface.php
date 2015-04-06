<?php

namespace Jimlei\FormHandler;

interface FormBuilderInterface
{
    function build(Form $form);
    function buildField($type, $name, $value);
}