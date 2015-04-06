<?php

namespace Jimlei\FormHandler;

class FormBuilderBootstrap implements FormBuilderInterface
{

    public function build(Form $form)
    {
        $data = $form->getData();
        $errors = $form->getErrors();
        $fields = array();

        var_dump($data);

        foreach ($form->getFields() as $name => $field)
        {
            $fieldValue = array_key_exists($name, $data) ? $data[$name] : null;
            $fieldErrors = array_key_exists($name, $errors) ? $errors[$name] : array();
            $fields[] = $this->buildField($field['type'], $name, $fieldValue, $fieldErrors);
        }

        return '<form method="post">' . implode('', $fields) . $this->buildSubmit() . '</form>';
    }

    public function buildField($type, $name, $value, $errors = array())
    {
        if ($type === 'string')
        {
            return $this->buildLabel($name) . '<input ' . (count($errors) !== 0 ? 'class="error" ' : '') . ' type="text" name="' . $name . '" value="' . $value . '" />';
        }
        elseif ($type === 'int')
        {
            return $this->buildLabel($name) . '<input type="number" name="' . $name . '" value="' . $value . '" />';
        }
    }

    public function buildLabel($name)
    {
        return '<label for="' . $name . '">' . $name . '</label>';
    }

    public function buildSubmit()
    {
        return '<input type="submit" value="Save" />';
    }

}