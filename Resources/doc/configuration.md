# Configuration

## Full configuration example

```yaml
#app/config/config.yml

ongr_api:
    versions:
        v3:
            endpoints:
                product: #this key represents endpoint name which will be used in URL 
                    repository: es.manager.default.product #required
                    methods: ["GET", "POST"] #default: GET, POST, PUT, DELETE
                    allow_extra_fields: false #default: false
                    allow_fields: ['name', 'surname', 'age'] #default: ~
                    allow_get_all: true #default: true
                    allow_batch: true #default: true
```

- `versions` - define multiple API versions. Version name will correspond to first url parameter e.g. `/v1`
> You should be consistent with version naming. Think of version naming conventions and stick to them e.g. `v1`, `v2`, `v2.2` ...

- `endpoints` - here you can define multiple API endpoints. Name will correspond to second url parameter, e.g. `/v3/product`. One endpoint is responsible for one elasticsearch type unless you specify custom controller see [more info here](custom_controller.md).

- `repository` - elasticsearch respository service name, e.g. `es.manager.default.product`

- `methods` - a list of methods that the API supports, each HTTP method represents action with the resource. e.g. PUT will update and create, DELETE will remove by id and etc..
 
- `allow_extra_fields` - when it's true, basically it turns off document structure validation. This means, that if you specify a value for a field that is not defined in your mapping, the field will be generated and a document in question will have the defined value in that field. To use it, it's necessary to create dynamic mapping for your `elasticsearch` type and configure `elasticsearch` to accept fields that are not from the mapping.

- `allow_fields` - if this option is set, API will allow only to operate with specified fields from the type.

- `allow_get_all` - adds `_all` to the endpoint and allows to get all values. Please keep in mind that results with `_all` will be paginated, therefore by default you will get only 10 documents. You can set `size` and `from` to modify output.

- `allow_batch` - adds `_batch` to the endpoint. This means that you can send an array of documents to be indexed to the particular endpoint type.


## What's next?

Let's learn more about [endpoints](endpoints.md).
