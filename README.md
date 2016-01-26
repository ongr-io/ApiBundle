# ONGR Api Bundle


> `master` branch docs might not be accurate. Until 1.0 will be released take a look at the [docs here](https://github.com/ongr-io/ApiBundle/blob/0.1/Resources/doc/index.md)

Api Bundle allows rapid setup of RESTful API to simplify Elasticsearch data access for the remote clients.

[![Build Status](https://travis-ci.org/ongr-io/ApiBundle.svg?branch=master)](https://travis-ci.org/ongr-io/ApiBundle)

Documentation

The source of the documentation is stored in the `Resources/doc/` folder in this bundle.

[Read the API Bundle Documentation][2]


## Setup the bundle


> This example assumes that you already have configured Elasticsearch bundle. 
If you haven't, here's a quick [setup guide][3] on how to do it.

### Step 1: Install

Api bundle is installed using [Composer][4].

```bash
composer require ongr/api-bundle
```

### Step 2: Enable bundle in the AppKernel

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
        new ONGR\ApiBundle\ONGRApiBundle(),
    );
}
```

> API Bundle requires JMS Serializer to work with JSON and XML

### Step 3: Add configuration

Add minimal configuration for Api bundle to the `config.yml`.

```yaml
#app/config/config.yml

ongr_api:
    default_encoding: json
        versions:
            v3:
                endpoints:
                    product:
                        repository: es.manager.default.product
```

> The example above shows a minimal configuration. To learn more take a look at the [configuration page][5].


Add routing

```yaml
#app/config/routing.yml

ongr_api_routing:
    resource: @ONGRApiBundle/Resources/config/routing.yml
    prefix: /api
```

> You can specify prefix like you want, `api` is only the example.

### Step 4: That's it

Its ready to use. API Bundle will generate new url endpoints by your configuration, by previous configuration you will have: `<yourdomain.com>/api/v3/product`

### What's next ?

Head to [configuration page][5] to learn how to get most of your API or take a look at the [basic usage example][6]


## License

This bundle is covered by the MIT license. Please see the complete license in the bundle [LICENSE][1] file.



[1]: LICENSE
[2]: Resources/doc/index.md
[3]: https://github.com/ongr-io/ElasticsearchBundle/blob/master/README.md
[4]: https://getcomposer.org/doc/00-intro.md
[5]: Resources/doc/configuration.md
[6]: Resources/doc/usage.md
