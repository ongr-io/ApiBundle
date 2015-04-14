Controller
==========

ONGRApiBundle has a built in default controller which allows you to use API functionality right after bundle install. It also allows you to define custom controllers if default functionality is not enough.

.. note:: Currently default api bundle controller implements only ``GET`` method. ``POST``, ``PUT`` and ``DELETE`` calls will return "Not implemented" Exception.

Custom controller
-----------------

If you want to perform custom actions or modify response, you can create your own controller and register it in config. Custom controller must implement ``ApiControllerInterface``.

Let's create new custom controller and call it ``AcmeApiController`` :

.. code:: php

    // Controller/AcmeApiController.php

    namespace Acme\DemoBundle\Controller;

    use ONGR\ApiBundle\ApiControllerInterface;

    class AcmeApiController implements ApiControllerInterface
    {
        public function getAction(Request $request)
        {
            // Custom logic ...

            return $response;
        }

        ...
    }

..

Then we must register it in our ``config.yml`` :

.. code:: yaml

    // app/config/config.yml
    ...
    ongr_api:
        versions:
            v1:
                endpoints:
                    picture:
                        controller:
                            name: AcmeDemoBundle:AcmeApi

..

That's it. Now every request for ``/v1/picture`` will be redirected to your controller.


Custom controller parameters
----------------------------

When defining custom controller you may also need to pass some parameters or define requirements for a route. ONGRApiBundle allows you to do this in config. Here is full possible controller configuration:

.. code:: yaml

    // app/config/config.yml
    ...
    ongr_api:
        versions:
            version_name:
                endpoints:
                    endpoint_name:
                        controller:
                            name: string
                            path: string
                            defaults: []
                            requirements: []
                            options: []
                            params: []

..

Here is an explanation of all parameters:

- ``name`` : logical controller name
- ``path`` (optional) : path, where your controller will be available (same as in Symfony routing)

 Examples:
    +-------------------+-----------------------------------------+------------------+
    | ``path`` value    | Route                                   | Type             |
    +===================+=========================================+==================+
    | ``/custom``       | /version_name/endpoint_name/custom      | Static route     |
    +-------------------+-----------------------------------------+------------------+
    | ``/custom/{id}``  | /version_name/endpoint_name/custom/{id} | Dynamic route.   |
    +-------------------+-----------------------------------------+------------------+

.. note:: Just like in Symfony routing, you can define dynamic routes with parameters, which can be used in your controller.

- ``defaults`` (optional) : array of default values (same as in Symfony routing)
- ``requirements`` (optional) : array of parameter requirements (same as in Symfony routing)
- ``options`` (optional) : array of options (same as in Symfony routing)
- ``params`` (optional) : array of additional parameters which will be passed to controller together with all endpoint config

Example usage:

.. code:: yaml

    controller:
        name: AcmeDemoBundle:AcmeApi
        path: /{id}
        defaults:
            id: 1
        requirements:
            id: \d+
        options:
            compiler_class: AcmeDemoBundle\Routing\RouteCompiler
        params:
            simple_param: 123
            array_param:
                some_string: string
                some_int: 321

..
