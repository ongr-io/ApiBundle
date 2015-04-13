Configuration
=============

Api bundle requires configuration for building API interface and easing backend work for developers.

Config.yml structure
--------------------

.. code:: yaml

    ongr_api:
        versions:
            version_name:
                endpoints:
                    endpoint_name:
                        manager: manager_name
                        controller: YourBundle:ControllerName
                        document: YourBundle:YourDocument
                            include_fields:
                                - field_name1
                                - field_name2
                            exclude_fields:
                                - field_name3
                                - field_name4
                        parent: parent_endpoint_name

- ``versions`` : here you should define multiple API versions

.. code:: yaml

    versions:
        version_name1:
            ...
        version_name2:
            ...

- ``version_name`` : version name that will correspond to first url parameter e.g. '/v1'
.. note:: You should be consistent with version naming. Think of version naming conventions and stick to them e.g. ``v1``, ``v2`` ... or ``1.0``, ``2.0`` ...

- ``endpoints`` : here you should define multiple API endpoints

.. code:: yaml

    endpoints:
        endpoint_name1:
            ...
        endpoint_name2:
            ...

- ``endpoint_name`` : endpoint name that will correspond to second url parameter e.g. '/v1/users'

- ``manager`` : elasticsearch manager. Default: ``es.manager.default``

- ``document`` : defined document type. Read more about defining elasticsearch documents `here <http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/mapping.html>`_

- ``controller`` (optional) : if you wish to customize default actions or implement your own, you should define a custom controller. If no controller is specified, ``ONGRApiBundle:Api`` controller is called. You can read more about customizing controller `here <controller.rst>`_

- ``include_fields`` (optional) : in case you want to allow access only to certain fields of the document you can define those fields here

- ``exclude_fields`` (optional) : in case you want to block access to some fields, you can define them here

- ``parent`` (optional) : if you define a parent endpoint your endpoint will inherit all the properties from parent and you can still define new ones
