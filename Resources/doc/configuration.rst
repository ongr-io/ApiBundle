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
                        manager: manager_service_name
                        document: BundleName:DocumentName
                        include_fields:
                            - field1_name
                            - ...
                        exclude_fields:
                            - field3_name
                            - ...
                        controller:
                            name: BundleName:ControllerName
                            path: /{param_name}/...
                            defaults:
                                param_name: param_value
                                ...
                            requirements:
                                param_name: param_requirements
                                ...
                            options:
                                - option_name: option_value
                                - ...
                            params:
                                param_name: param_value
                                ...
                parent: parent_version_name

..

- ``versions`` : here you should define multiple API versions

.. code:: yaml

    versions:
        version_name1:
            ...
        version_name2:
            ...

..

- ``version_name`` : version name that will correspond to first url parameter e.g. '/v1'

.. note:: You should be consistent with version naming. Think of version naming conventions and stick to them e.g. ``v1``, ``v2`` ... or ``1.0``, ``2.0`` ...

- ``endpoints`` : here you should define multiple API endpoints

.. code:: yaml

    endpoints:
        endpoint_name1:
            ...
        endpoint_name2:
            ...

..

- ``endpoint_name`` : endpoint name that will correspond to second url parameter, e.g. '/v1/users'

- ``manager`` : elasticsearch manager name, e.g. 'es.manager.default'

- ``document`` : defined document type. Read more about defining elasticsearch documents `defining elasticsearch documents <http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/mapping.html>`_

- ``include_fields`` (optional) : in case you want to allow access only to certain fields of the document, you can define those fields here. Can not be used together with ``exclude_fields``

- ``exclude_fields`` (optional) : in case you want to block access to some fields, you can define it here. Can not be used together with ``include_fields``

- ``controller`` (optional) : if you wish to customize controller actions, you should define a custom controller. If no controller is specified, ``ONGRApiBundle:Api`` controller is used. You can read more about customizing controller `here <controller.rst>`_

- ``parent`` (optional) : if you wish to inherit endpoints from another version, you can define parent version. Inherited endpoints can be also overridden
