# Custom controller

Sometimes might be that default functionality is not enough. So for this reason you can create/extend current controller or create your own one.

There are two types of controllers:
 - `RestController` extends `AbstractRestController` implements `RestControllerInterface`. Handles normal endpoint requests.
 - `BatchController` extends `AbstractRestController` implements `BatchControllerInterface`. Handles only **POST** requests.

ONGRApiBundle comes with default controllers which allows you to use API functionality right after bundle install.


## How to

Your new custom controller must implement same interfaces and extend same parents like default ones.

Let's create a new custom REST controller and call it `AppRestController` :

```php
<?php
// src/YourBundle/Controller/AcmeRestController.php

namespace AppBundle\Controller;

use ONGR\ApiBundle\Controller\AbstractRestController;
use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequest;

class AppRestController extends AbstractRestController implements RestControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest)
    {
        $data = $restRequest->getData();

        // Custom logic ...

        return $this->renderRest($data);
    }

    //...
}
```

Add a route to the `routing.yml`.

It's a simple route like all other in your app. How to add routing read [the official Symfony docs](http://symfony.com/doc/current/book/routing.html). 

