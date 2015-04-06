<?php

namespace Jimlei\FormHandler;

class FormBuilderPlain implements FormBuilderInterface
{

    public function build(Form $form)
    {
        $data = $form->getData();
        $errors = $form->getErrors();
        $fields = array();

        var_dump($data);

        foreach ($form->getFields() as $name => $field)
        {
            $value = array_key_exists($name, $data) ? $data[$name] : null;
            $fields[] = $this->buildField($field['type'], $name, $value);
        }

        return '<form method="post">' . implode('', $fields) . $this->buildSubmit() . '</form>';
    }

    public function buildField($type, $name, $value, $error = false)
    {
        if ($type === 'string')
        {
            return $this->buildLabel($name) . '<input type="text" name="' . $name . '" value="' . $value . '" />';
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