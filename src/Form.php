<?php

namespace Jimlei\FormHandler;

use Jimlei\FormHandler\RequestInterface;

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

        if (empty($this->errors))
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
        return empty($this->errors);
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
        if (empty($this->data[$field]))
        {
            // This field was not submitted, check if it's required
            if (isset($requirements['required']) && $requirements['required'])
            {
                $this->errors[$field][] = 'Required field missing';
            }
            return;
        }

        foreach ($requirements as $requirement => $value)
        {
            if ($requirement === 'type')
            {
                if (!$this->validateFieldType($field, $value))
                {
                    $this->errors[$field][] = 'Invalid type, should be ' . $value;
                }
            }
            else if ($requirement === 'minLength')
            {
                if (!$this->validateFieldMinLength($field, $value))
                {
                    $this->errors[$field][] = 'Invalid field length, should be longer than or equal to ' . $value;
                }
            }
            else if ($requirement === 'maxLength')
            {
                if (!$this->validateFieldMaxLength($field, $value))
                {
                    $this->errors[$field][] = 'Invalid field length, should be less than or equal to ' . $value;
                }
            }
        }
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
        if ($type === 'int' && $this->data[$field] == (int) $this->data[$field])
        {
            return false;
        }
        else if ($type === 'email' && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        else if ($type === 'timestamp')
        {
            $stamp = $this->data[$field];
            return ((string) (int) $stamp) === $stamp && ($stamp <= PHP_INT_MAX) && ($stamp >= ~PHP_INT_MAX);
        }

        // todo throw error on invalid field (?)
        return true;
    }
}