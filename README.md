# ONGR Api Bundle


# Currently API Bundle is under reconstruction. Use 0.1 branch as a stable version for now. 

> `master` branch docs might not be accurate. Until 1.0 will be released take a look at the [docs here](https://github.com/ongr-io/ApiBundle/blob/0.1/Resources/doc/index.md)

Api Bundle allows rapid setup of RESTful API to simplify Elasticsearch data access for remote clients.

[![Build Status](https://travis-ci.org/ongr-io/ApiBundle.svg?branch=master)](https://travis-ci.org/ongr-io/ApiBundle)

Documentation

The source of the documentation is stored in the `Resources/doc/` folder in this bundle.

[Read the API Bundle Documentation][2]

## Setup the bundle

#### Step 1: Configure Elasticsearch bundle

This example assumes that you already have configured Elasticsearch bundle.
If you haven't, here's a quick [setup guide][3] on how to do it.

#### Step 2: Install Api bundle

Api bundle is installed using [Composer][4].

```bash
composer require ongr/api-bundle
```


#### Step 3: Enable Api bundle

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

> API Bundle requires JMS Serializer to work with json and xml


#### Step 4: Add configuration


##### Add minimal configuration for Api bundle to the `config.yml`.

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


> If you have altered default Elasticsearch bundle configuration or simple configuration is not enough, you might need 
to change ``manager`` and ``documents``. Head to [configuration page][5] for more information.


##### Add routing

```yaml
#app/config/routing.yml

ongr_api_routing:
    resource: @ONGRApiBundle/Resources/config/routing.yml
    prefix: /api
```

#### Step 5: That's it

Its ready to use. API Bundle will generate new url endpoints by your configuration, by previous configuration you will have: `<yourdomain.com>/api/v3/product`

#### What's next ?

Head to [configuration page][5] to learn how to get most of your API or take a look at the [basic usage example][6]

## License

This bundle is covered by the MIT license. Please see the complete license in the bundle [LICENSE][1] file.



[1]: https://raw.githubusercontent.com/ongr-io/ApiBundle/master/LICENSE
[2]: https://github.com/ongr-io/ApiBundle/blob/master/Resources/doc/index.md
[3]: https://github.com/ongr-io/ElasticsearchBundle/blob/master/README.md
[4]: https://getcomposer.org/doc/00-intro.md
[5]: https://github.com/ongr-io/ApiBundle/blob/master/Resources/doc/configuration.md
[6]: https://github.com/ongr-io/ApiBundle/blob/master/Resources/doc/usage.md
