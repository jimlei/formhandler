<?php

namespace Jimlei\FormHandler;

/**
 * Form handler class that maps a request to an entity if the fields are valid
 *
 * example usage:
 *
 * class ObjectForm
 * {
 *
 *   public function __construct(Object $object)
 *   {
 *     $fields = array(
 *       'name' => array(
 *         'type' => 'string',
 *         'maxLength' => '255',
 *         'required' => true
 *       ),
 *       'price' => array(
 *         'type' => 'int'
 *       )
 *     );
 *
 *     parent::__construct($object, $fields);
 *   }
 * }
 *
 * $request = new Request();
 *
 * $object = new Object();
 * $form = new ObjectForm($object);
 * $form->handleRequest($request);
 *
 * if ($form->isValid())
 * {
 *   // save object
 *   $this->em->persist($object);
 *   $this->em->flush();
 * }
 *
 * if ($form->getErrors())
 * {
 *   // do something with the errors
 *   foreach ($form->getErrors() as $error)
 *   {
 *     $this->flash('danger', $error);
 *   }
 * }
 *
 */
abstract class Form
{
    /**
     * @var
     */
    private $entity;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param mixed $entity
     * @param array  $fields
     */
    public function __construct($entity, $fields)
    {
        $this->data = array();
        $this->entity = $entity;
        $this->errors = array();
        $this->fields = $fields;
    }

    /**
     * Renders form using FormBuilder
     * @return string
     */
    public function render()
    {
        $builder = new FormBuilderPlain();
        $form = $builder->build($this);

        return $form;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param RequestInterface $request
     */
    public function handleRequest(RequestInterface $request)
    {
        // get form data from request
        foreach($request->getData() as $submittedField => $submittedValue)
        {
            if ($this->hasField($submittedField))
            {
                // submitted data belongs to the form
                $this->data[$submittedField] = $submittedValue;
            }
        }

        // validate according to field requirements
        foreach ($this->fields as $field => $requirements)
        {
            $this->validateField($field, $requirements);
        }

        if (count($this->errors) === 0)
        {
            // No errors, lets update the entity
            $this->updateEntity();
        }
    }

    /**
     * Check if form is valid
     * If we have no errors we have a valid form!
     *
     * @return bool
     */
    public function isValid()
    {
        return count($this->errors) === 0;
    }

    /**
     * Check if form contains field
     *
     * @param string $field
     * @return bool
     */
    private function hasField($field)
    {
        return array_key_exists($field, $this->fields);
    }

    /**
     * @param string $property
     * @param mixed  $value
     */
    private function setEntityProperty($property, $value)
    {
        // todo: throw error on invalid property (?)
        if (property_exists($this->entity, $property))
        {
            $this->entity->{'set'.ucfirst($property)}($value);
        }
    }

    /**
     * Updates the entity with form data
     */
    private function updateEntity()
    {
        foreach ($this->data as $key => $value)
        {
            $this->setEntityProperty($key, $value);
        }
    }

    /**
     * Validates a field using requirements
     *
     * @param string $field
     * @param array  $requirements
     */
    private function validateField($field, $requirements)
    {
        if (!array_key_exists($field, $this->data))
        {
            // This field was not submitted, check if it's required
            if (array_key_exists('required', $requirements) && $requirements['required'])
            {
                $this->errors[$field][] = 'Required field missing';
            }
            return;
        }

        foreach ($requirements as $requirement => $value)
        {
            if ($requirement === 'type' && !$this->validateFieldType($field, $value))
            {
                $this->errors[$field][] = 'Invalid type, should be ' . $value;
            }
            elseif ($requirement === 'min' && !$this->validateFieldMin($field, $value))
            {
                $this->errors[$field][] = 'Invalid field, should be larger than or equal to ' . $value;
            }
            elseif ($requirement === 'max' && !$this->validateFieldMax($field, $value))
            {
                $this->errors[$field][] = 'Invalid field, should be less than or equal to ' . $value;
            }
            elseif ($requirement === 'minLength' && !$this->validateFieldMinLength($field, $value))
            {
                $this->errors[$field][] = 'Invalid field length, should be longer than or equal to ' . $value;
            }
            elseif ($requirement === 'maxLength' && !$this->validateFieldMaxLength($field, $value))
            {
                $this->errors[$field][] = 'Invalid field length, should be less than or equal to ' . $value;
            }
        }
    }

    /**
     * @param string $field
     * @param int    $value
     * @return bool
     */
    private function validateFieldMin($field, $value)
    {
        return (int) $this->data[$field] >= $value;

    }

    /**
     * @param string $field
     * @param int    $value
     * @return bool
     */
    private function validateFieldMax($field, $value)
    {
        return (int) $this->data[$field] <= $value;

    }

    /**
     * @param string $field
     * @param int    $length
     * @return bool
     */
    private function validateFieldMinLength($field, $length)
    {
        return strlen($this->data[$field]) >= $length;

    }

    /**
     * @param string $field
     * @param int    $length
     * @return bool
     */
    private function validateFieldMaxLength($field, $length)
    {
        return strlen($this->data[$field]) <= $length;

    }

    /**
     * @param string $field
     * @param string $type
     * @return bool
     */
    private function validateFieldType($field, $type)
    {
        if ($this->data[$field] !== '')
        {
            if ($type === 'bool')
            {
                // make it play nice with FILTER_VALIDATE
                $type = 'boolean';
            }

            if (in_array($type, array('float', 'boolean', 'email', 'int', 'ip', 'url'), true))
            {
                return filter_var($this->data[$field], constant('FILTER_VALIDATE_' . strtoupper($type)));
            }
            elseif ($type === 'timestamp')
            {
                return is_numeric($this->data[$field])
                && ($this->data[$field] <= PHP_INT_MAX)
                && ($this->data[$field] >= ~PHP_INT_MAX);
            }
        }

        // todo throw error on invalid type (?)
        return true;
    }
}