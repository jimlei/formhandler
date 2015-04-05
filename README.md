## INSTALLATION INSTRUCTIONS

Include with composer

```
composer require jimlei/formhandler:dev-master
```

Create the object/entity that will be modified by the form (request)

```php
<?php // src/Entity/Article.php

namespace Acme\Entity;

class Article
{
    private $id;
    private $title;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
}
```

Create the form that will map/validate the data in the request

```php
<?php // src/Form/ArticleForm.php

namespace Acme\Form;

use Acme\Entity\Article;
use Jimlei\FormHandler\Form;

class ArticleForm extends Form
{
    public function __construct(Article $article)
    {
        $fields = array(
            'title' => array(
                'type' => 'string',
                'maxLength' => '60',
                'required' => true
            )
        );

        parent::__construct($article, $fields);
    }
}
```

It's depending on the Request->getData method so you should implement the RequestInterface in your handling of request data (method, data, query, etc).

```php
<?php // src/Net/Request.php

namespace Acme\Net;

use Jimlei\FormHandler\RequestInterface;

/**
 * Maps a request to usable data.
 */
class Request implements RequestInterface
{
    private $data;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents('php://input'));
    }
    
    public function getData()
    {
        return $this->data;
    }
}
```

Bring it together

```php
<?php // index.php

use Acme\Entity\Article;
use Acme\Form\ArticleForm;
use Acme\Net\Request;

require 'vendor/autoload.php';

$request = new Request();
$article = new Article();

$form = new ArticleForm($article);
$form->handleRequest($request);

if ($form->isValid())
{
    // save article...
}

// do something with the errors
foreach ($form->getErrors() as $error)
{
  // log, add to flash message, display otherwise, etc.
}
```

#### Available types

* int
* string
* email
* ~~float~~
* ~~time~~
* ~~date~~
* ~~datetime~~

#### Available validations

* required (bool)
* ~~min (int)~~
* ~~max (int)~~
* ~~minLength (int)~~
* maxLength (int)

## Run tests
```
$ vendor/bin/phpunit
```
