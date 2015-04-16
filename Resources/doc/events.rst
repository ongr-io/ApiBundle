DataRequest Events
==================

Bundle provides a way to alter default behavior via events. There are 2 events ``PreGetEvent`` and ``PostGetEvent``.

PreGetEvent
-----------
This event allows altering search request to ES. Event name will be *ongr.api.{endpointName}.pre.get*
It is dispatched right before sending search request to ES with following parameters:

 - search
 - request
 - repository

Example listener:

.. code:: yaml

    acme.api.pre.get.listener:
        class: Acme\AcmeBundle\EventListener\ProductListener
        tags:
            - { name: kernel.event_listener, event: ongr.api.endpoint.pre.get, method: onPreGet }
..

.. code:: php

    class ProductListener
    {
        public function onPreGet(PreGetEvent $event, $eventName)
        {
            // Code.
        }
    }

..

PostGetEvent
------------
This event allows altering API response. Event name will be *ongr.api.{endpointName}.post.get*
It is dispatched right before sending search request to ES with following parameters:

 - request
 - result

Example listener:

.. code:: yaml

    acme.api.pre.get.listener:
        class: Acme\AcmeBundle\EventListener\ProductListener
        tags:
            - { name: kernel.event_listener, event: ongr.api.endpoint.post.get, method: onPostGet }
..

.. code:: php

    class ProductListener
    {
        public function onPostGet(PostGetEvent $event, $eventName)
        {
            // Code.
            $event->setResult($newResult);
        }
    }

..
