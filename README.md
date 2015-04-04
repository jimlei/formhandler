### INSTALLATION INSTRUCTIONS
#### Pre-alpha

1. composer require jimlei/formhandler

2. Create an object to use as a store Entity

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

class ArticleForm extends Form
{
    public function __construct(Article $article)
    {
        $fields = array(
            'title' => array(
                'type' => 'string',
                'maxLength' => '255',
                'required' => true
            ),
            'text' => array(
                'type' => 'string',
                'maxLength' => '5000',
                'required' => true
            )
        );

        parent::__construct($article, $fields);
    }
}
```

And lastly your controller (implementation depends on your framework)

```php
<?php

namespace Acme\Controller;

use Acme\Entity\Article;
use Acme\Form\ArticleForm;
use Jimlei\FormHandler\Request;

class ArticleController
{
    public function EditAction()
    {
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
    }
}
```