Setup
=====

Step 1: Configure Elasticsearch bundle
--------------------------------------

This example assumes that you already have configured Elasticsearch bundle.
If you haven't, here's a quick [setup guide][1] on how to do it.

Step 2: Install Api bundle
--------------------------

Api bundle is installed using [Composer][2].

```
composer require ongr/api-bundle
```


Step 3: Enable Api bundle
-------------------------

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
    	// ...
    	new JMS\SerializerBundle\JMSSerializerBundle(),
        new ONGR\ApiBundle\ONGRApiBundle(),
    );
}
```

Step 4: Add configuration
-------------------------

Add minimal configuration for Api bundle.

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
                            - "ONGRDemoBundle:Product"
                            - "ONGRDemoBundle:Category"
```


> If you have altered default Elasticsearch bundle configuration, you might need to change ``manager`` and ``documents``. Head to [configuration page][3] for more information.

Add routing.

```yaml
#app/config/routing.yml

ongr_api_routing:
	resource: @ONGRApiBundle/Resources/config/routing.yml
    prefix: /api
```

Step 5: That's it
-----------------

Try your new API. Open `<yourdomain.com>/api/v1/product` page in your browser. You should receive encoded product list.

What's next ?
-------------

Head to [configuration page][3] to learn how to get most of your API.

[1]: http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/setup.html
[2]: https://getcomposer.org
[3]: configuration.md
