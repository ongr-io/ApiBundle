# Endpoints

## Crud actions

Rest API endpoints rely on the configuration you have set. How to setup bundles configuration [can be found here][1]. The uri pattern is made of *version* and *endpoint name* you wish to access and also an optional *id* for requesting specific documents. Pattern:

```
<yourdomain.com>/api/{version}/{endpoint}/{id}
```

POST and PUT methods should contain data to insert in their request body.

Default controller status codes:

| Method | Success | Error | Extra                                                                  |
|--------|---------|-------|------------------------------------------------------------------------|
| GET    | 200     | 404   | 404 if not found.                                                      |
| POST   | 201     | 400   | 406 validation error, 409 resource exists.                             |
| PUT    | 204     | 400   | 406 validation error.                                                  |
| DELETE | 204     | 400   | 404 if not found.                                                      |

As you would expect, `GET` requests are used to retrieve documents from the Elasticsearch, `DELETE` is used for removing documents, PUT -for updating documents and POST is used for indexing. The difference between POST and PUT is that PUT requires you to specify the document id in the url, as mentioned above, and POST lets Elasticsearch create a document id.


## Multiple insert

Batch is useful if you need to pass many documents to an api via a single request.

```yaml
#app/config/config.yml

ongr_api:
    versions:
        v3:
            endpoints:
                product:
                    repository: es.manager.default.product
                    allow_batch: true #default: true
```
> By default batch is enabled but if you don't want to allow multiple documents insert, you can disable it.

To index multiple documents send either a POST or PUT request, or delete multiple documents by sending a DELETE request, to the new `_batch` endpoint. By provided example above it should look like this:

```
<yourdomain.com>/api/v3/product/_batch 
```

This API endpoint can be accessed by sending a POST request and it takes requests bodies in a specific structure. f.e.

```json
[
    {
        "title": "Tuna",
        "sku": "FT10012",
        "price": 19.99
    },
    {
        "_id": "SS10043",
        "title": "Salmon",
        "sku": "SS10043",
        "price": 9.99
    }
]
```

Note that both PUT and DELETE will require you to send the _id for every document.

API will return a response with created products ID's if no error occur. e.g. response:

```json
{
    "took" : 13,
    "errors" :false,
    "items" : [
        {
            "create" : {
                "_index" : "ongr_api_test",
                "_type" : "product",
                "_id" : "AVPLTR8LO-XYW3LRUZyP",
                "_version" : 1,
                "_shards" : {
                    "total" : 2,
                    "successful" : 1,
                    "failed" : 0
                },
                "status" : 201
            }
        },
        {
            "create" : {
                "_index" : "ongr_api_test",
                "_type" : "product",
                "_id" : "SS10043",
                "_version" : 1,
                "_shards" : {
                    "total" : 2,
                    "successful" : 1,
                    "failed" : 0
                },
                "status" : 201
            }
        }
    ]
}
```

### Overview

| Method | Full document body | _id field  | Resulting Action              |
|--------|--------------------|------------|-------------------------------|
| POST   | required           | Optional   | Creation, no update           |
| PUT    | required           | Required   | Update, creation if necessary |
| DELETE | will be ignored    | Required   | Deletion                      |


## Get all documents

There is an option to retrieve all documents from a specific `repository`. 

```yaml
#app/config/config.yml

ongr_api:
    versions:
        v3:
            endpoints:
                product:
                    repository: es.manager.default.product
                    allow_get_all: true #default: true
```

When the `allow_get_all` is set to `true` new route will be generated, e.g. :
 
```
<yourdomain.com>/api/v3/product/_all
```

This API endpoint only takes **GET** requests. Without any options you will get first 10 documents. In addition there are some options which can be accepted:


| Option | Default | Description                                                          |
|--------|---------|----------------------------------------------------------------------|
| size   | 10      | Parameter defines the amount of documents to fetch.                  |
| from   | 0       | Parameter defines the offset from the first result you want to fetch.|

Say we want to get 50 documents then request will look like:

```
<yourdomain.com>/api/v3/product/_all?size=50
```

and another 50:

```
<yourdomain.com>/api/v3/product/_all?size=50&from=50
```


Customization
-------------
How to customize request body and response can be found [here][2].

[1]: configuration.md
[2]: custom_controller.md
