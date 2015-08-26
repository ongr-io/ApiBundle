Configuration
=============

Full configuration example:

```yaml
#app/config/config.yml

ongr_api:
    authorization:
        enabled: true
        secret: "supersecretstring"
    default_encoding: json
    versions:
        v1:
            batch:
                enabled: true
                controller: "ongr_api.batch_controller"
            endpoints:
                default:
                    manager: es.manager.default
                    documents:
                        - { name: "ONGRDemoBundle:Product", controller: "ongr_api.rest_controller", methods: ["GET", "POST"] }
                        - "ONGRDemoBundle:Category"
                custom:
                    manager: es.manager.mymanager
                    documents:
                        - { name: "MyVendorBundle", controller: "my_vendor.rest_controller" }
```

- `authorization` - If set, request header must include `Authorization` with value in this case 'supersecretstring'.

- `default_encoding` - default encoding used if unknown `Accept` value set in header.

- `versions` - define multiple API versions. Version name will correspond to first url parameter e.g. `/v1`
> You should be consistent with version naming. Think of version naming conventions and stick to them e.g. ``v1``, ``v2``, ...

- `endpoints` - here you can define multiple API endpoints. Name will correspond to second url parameter, e.g. `/v1/users`. One endpoint is responsible for one elasticsearch manager.

- `manager` - elasticsearch manager name, e.g. `es.manager.default`

- `documents` - exposed documents to API. Read more about defining elasticsearch documents [here][1]. For each document you can also define a custom controller with custom logic for your API and methods which will be available.
> About custom controllers read more [here][2].

What's next?
-------------
Let's learn more about [endpoints][3].

[1]: http://ongr.readthedocs.org/en/latest/components/ElasticsearchBundle/mapping.html
[2]: custom_controller.md
[3]: endpoints.md
