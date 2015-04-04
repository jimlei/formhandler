### INSTALLATION INSTRUCTIONS
#### Pre-alpha

Include the Formhandler

```
composer require jimlei/formhandler:dev-master
```

Create an object/entity that will be modified by a form (request)

```php
<?php

namespace Acme\Entity;

class Article
{
  private $id;
  private $title;
  private $text;

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

  public function getText()
  {
    return $this->text;
  }

  public function setText($text)
  {
    $this->text = $text;
    return $this;
  }
}
```

Then you need a form that will map/validate the data in the request

```php
<?php

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
      ),
      'text' => array(
        'type' => 'string',
        'maxLength' => '5000',
        'required' => true
      ),
      'publishDate' => array(
        'type' => 'datetime',
      )
    );

    parent::__construct($article, $fields);
  }
}
```

Piecing it together

```php
<?php

use Acme\Entity\Article;
use Acme\Form\ArticleForm;
use Jimlei\FormHandler\Request;

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
  $this->flash('danger', $error);
}
```

#### Types

* string
* int
* float
* time
* date
* datetime

#### Validations

* required (bool)
* min (int)
* max (int)
* minLength (int)
* maxLength (int)
