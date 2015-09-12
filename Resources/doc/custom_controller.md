Custom controller
=================

There are three types of controllers:
 - `RestController` extends `AbstractRestController` implements `RestControllerInterface`. Handles batch requests.

 - `BatchController` extends `AbstractRestController` implements `BatchControllerInterface`. Handlles *post*, *get*, *put*, *delete* requests.
 
 - `CommandController` extends `AbstractRestController` implements `CommandControllerInterface`. Handles command requests if they are enbaled in [configuration][1].

ONGRApiBundle comes with default controllers which allows you to use API functionality right after bundle install. It also allows you to define custom controllers if default functionality is not enough.

How to
------

Your new custom controller must implement same interfaces and extend same parents like default ones.

Let's create new custom rest controller and call it `AcmeRestController` :

```php
<?php
// Controller/AcmeRestController.php

namespace Acme\DemoBundle\Controller;

use ONGR\ApiBundle\Controller\AbstractRestController;
use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequest;

class AcmeRestController extends AbstractRestController implements RestControllerInterface
{
	/**
	 * {@inheritdoc}
	 */
    public function getAction(RestRequest $restRequest, $id = null)
    {
		$data = $restRequest->getData();

        // Custom logic ...

        return $this->renderRest($data);
    }

    ...
}
```

Then we must register it in our service container:

```yaml
#src/Acme/DemoBundle/Resouces/config.yml

services:
	acme_demo.rest_controller:
		class: Acme\DemoBundle\Controller\AcmeRestController
		calls:
			- ['setContainer', [@service_container]]
```

And lastly tell ONGR API bundle to use it:

```yaml
#app/config/config.yml

ongr_api:
    default_encoding: json
        versions:
            v1:
                endpoints:
                    default:
                        manager: es.manager.default
                        documents:
                            - { name: "ONGRDemoBundle:Product", controller: "acme_demo.rest_controller" }
```

That's it. Now every request for `/v1/product` will be handled by our new controller.

[1]: configuration.md
