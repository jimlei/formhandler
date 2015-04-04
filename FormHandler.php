<?php
// just to get our dumps looking good
echo'<pre>';

/**
 * Maps a request to usable data.
 */
class Request
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var mixed
     */
    private $query;

    // etc

    /**
     * Should convert global data and other input to be usable in the request object
     */
    public function __construct()
    {
        // mock of json_decode(file_get_contents('php://input'))
        $this->data = json_decode('{"name": "jimlei", "email": "jim.leirvik@gmail.com"}');
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}

/**
 * Abstract class Entity
 */
abstract class Entity {}

/**
 * User Entity
 * The actual object that is persisted to storage
 */
class User extends Entity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $age;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }
}

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
     * @var Entity
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
     * @param Entity $entity
     * @param array  $fields
     */
    public function __construct(Entity $entity, $fields)
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
     * @param Request $request
     */
    public function handleRequest(Request $request)
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

        // todo throw error on invalid field (?)
        return true;
    }
}

/**
 * Class UserForm
 * Validates and maps a request to an entity
 */
class UserForm extends Form
{
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $fields = array(
            'name' => array(
                'type' => 'string',
                'maxLength' => '255',
                'required' => true
            ),
            'email' => array(
                'type' => 'email',
                'maxLength' => '255',
                'required' => true
            ),
            'age' => array(
                'type' => 'int'
            )
        );

        parent::__construct($user, $fields);
    }
}

// controller
$request = new Request();

$user = new User();

$form = new UserForm($user);
$form->handleRequest($request);

if ($form->isValid())
{
    // here you would probably save the user
    var_dump('Form IS valid :D');
}
else
{
    var_dump('Form is NOT valid :(');
}

print_r($user);
print_r('Errors ');
print_r($form->getErrors());
