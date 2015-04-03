Controller
==========

Every api bundle controller must implement ``ApiControllerInterface``.

Default api bundle controller implements only ``GET`` method. ``POST``, ``PUT`` and ``DELETE`` calls will return "Not implemented" Exception.

Custom controller
-----------------

If you want to perform custom actions or modify response, you can create your own controller and register it in config.

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

Then we must register it in our ``config.yml`` :
.. code:: yml
    // app/config/config.yml
    ...
    ongr_api:
        versions:
            v1:
                endpoints:
                    picture:
                        controller: "AcmeDemoBundle:AcmeApi"

That's it. Now every request for ``/v1/picture`` will be redirected to your controller.