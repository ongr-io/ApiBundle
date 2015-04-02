Setup
=====

Step 1: Configure Elasticsearch bundle
--------------------------------------

This example assumes that you have already configured Elasticsearch bundle.
If you haven't, here's a quick guide on how to do it `Elastic search setup <http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/setup.html>`_.

Step 2: Install Api bundle
--------------------------

Api bundle is installed using `Composer <https://getcomposer.org>`_.

.. code:: bash

    php composer.phar require ongr/api-bundle "master-dev"

.. note:: composer.phar file is typically found in your project's main dir e.g. 'var/www'

Step 3: Enable Api bundle
-------------------------

.. code:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new ONGR\ApiBundle\ONGRApiBundle(),
        );
    }

Step 4: Add configuration
-------------------------

Add minimal configuration for Api bundle.

.. code:: yaml

    #app/config/config.yml
    ...
    ongr_api:
       versions:
           v1:
               endpoints:
                   customer:
                       manager: es.manager.default
                       document: AcmeDemoBundle:Product

.. note:: If you have altered default Elasticsearch bundle configuration, you might need to change ``managers`` and ``documents``. Head to `configuration page<configuration.html>`_ for more information.

.. code:: yaml

    #app/config/routing.yml
    ...
    ONGRApiBundle_ApiRoute:
       resource: .
       type: apiroute

Step 5: That's it
-----------------

Try your new API. Type ``http://ongr.dev/api/v1/product`` in your browser. You should receive json encoded products list.

What's next ?
-------------

Head to `configuration page<configuration.html>`_ to learn how to customise your API.

