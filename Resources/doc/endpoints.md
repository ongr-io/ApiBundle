Endpoints
=========

Rest
----

Rest endpoints rely on configuration you have set. How to setup can be found [here][1]. The uri pattern is made of *version*, *endpoint name*, *document type* you whish to access and also an optional *id* for requesting specific documents. Pattern:

```
<yourdomain.com>/api/{version}/{endpoint}/{document_type}/{id}
```

If endpoint is set to *default* then pattern would look like this:

```
<yourdomain.com>/api/{version}/{document_type}/{id}
```

Status codes:

| Method | Success | Error | Extra                       |
|--------|---------|-------|-----------------------------|
| GET    | 200     | 410   |                             |
| POST   | 201     | 400   | includes `Location` header  |
| PUT    | 204     | 400   | includes `Location` header  |
| DELETE | 204     | 400   | 404 if not found            |

> Error's will mostly occur on invalid data in *POST* and *PUT* requests.

Batch
-----

Batch is useful if you need to pass many requests to an api on one connection. Every api version has their batch api enabled by default, but it can be disabled like so:

```yaml
#app/config/config.yml

ongr_api:
    default_encoding: json
        versions:
            v1:
				batch:
					enabled: false
                endpoints:
                    ...
```

This Api only takes ***post*** requests and in specific structure. f.e.

```json
[
    {
        "method": "POST",
        "path": "product/1",
        "body": {
            "title": "Tuna",
            "sku": "FT10012",
            "description": "Refero Eluo fornax vos illa ora Nutus casus moderor hoc Fides, revolvo vox corium ne eo Decoro.",
            "price": 9.99,
            "category": ["5"],
            "image": "/media/image/tuna.jpg",
            "urls": [
                {
                    "url": "fish/tuna.html",
                    "key": ""
                }
            ]
        }
    },
    {
        "method": "GET",
        "path": "product/1",
        "body": []
    },
	{
        "method": "GET",
        "path": "product/2",
        "body": []
    }
]
```

This batch will create a new product with id of `1` and fetch two products with id of `1` and `2`. As you can tell second batch request should return the same data that we put in first request.
> Logic is used from Rest controller which is resolved by provided path in each request.

Response should be similar to this:

```json
[
	{
		"status_code": 201
	},
	{
		"status_code": 200,
		"response": {
			"title": "Tuna",
            "sku": "FT10012",
            "description": "Refero Eluo fornax vos illa ora Nutus casus moderor hoc Fides, revolvo vox corium ne eo Decoro.",
            "price": 9.99,
            "category": ["5"],
            "image": "/media/image/tuna.jpg",
            "urls": [
                {
                    "url": "fish/tuna.html",
                    "key": ""
                }
            ]
		}
	},
	{
		"status_code": 410
	}
]
```

Last request got 410 response because document did not exist.
> Batch Api returns **202** status code on success.

Customization
-------------
How to customize request body and response can be found [here][2].

[1]: setup.md
[2]: custom_controller.md
