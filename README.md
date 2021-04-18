# Icon Search

Simple API to search and filter icons.

## Simple UI consuming API

> https://iconscout-test.herokuapp.com/

## Admin Panel

> https://iconscout-test.herokuapp.com/admin

---

## Installation

1. Clone the project using git
2. Create `.env` file from `.env.example` file and set database parameters, elastic host and App Key
3. Run `composer install`
4. Run `php artisan migrate:fresh --seed` to migrate and seed table
5. Run `php artisan seed:icons_to_db` to seed icons from external source
6. Run `php artisan import:data_to_elasticsearch` to export data to elastic search

## Seed Users

Team Admin -

```
{
    "email" : "johndoe@iconscout.com",
    "password" : "password"
}
```

Team Member -

```
{
    "email" : "jamesdoe@iconscout.com",
    "password" : "password"
}
```

API user -

```
{
    "email" : "pauldoe@iconscout.com",
    "password" : "password"
}
```

## API Endpoints

## GET

Search

> https://iconscout-test.herokuapp.com/api/v1/search

## POST

Login

> https://iconscout-test.herokuapp.com/api/v1/user/login

---

### GET Search

Search Icons

**Parameters**

|            Name | Required |     Type     | Description                                               |
| --------------: | :------: | :----------: | --------------------------------------------------------- |
| `Authorization` | required | Bearer Token | Auth user bearer token.                                   |
|         `query` | required |    String    | Icon Search Term.                                         |
|         `style` |    -     |    String    | Filter style type (Flat, Line, Dualtone, Colored Outline) |
|         `price` |    -     |    String    | Filter price (Free, Premium, Custom price(5, 7))          |
|         `color` |    -     |    String    | Filter color (HSL, RGB formats)                           |
|    `color_type` |    -     |    String    | Filter color type(HSL, RGB), defualts to HSL              |
|          `page` |    -     |    String    | Page number, defaults to 1                                |
|      `per_page` |    -     |    String    | Results per page, defaults to 20                          |

**Response**

```

// Sample Success
Status Code- 200
{
    "status": "success",
    "response": {
        "aggregations": {
            "style": [
                {
                    "name": "Line",
                    "count": 78
                }
            ]
        },
        "items": {
            "current_page": 1,
            "data": [
                {
                    "id": 194,
                    "name": "Business",
                    "img_url": "https://s3.wasabisys.com/icons-dev/icon/premium/png-256-thumb/114.png",
                    "style": "Line",
                    "price": "2.00",
                    "categories": [
                        {
                            "id": 154,
                            "value": "Business"
                        }
                    ],
                    "colors": [
                        {
                            "id": 264,
                            "hex_value": "373431",
                            "hsl_value": "8,5,20",
                            "dec_value": null,
                            "name": null
                        }
                    ],
                    "tags": [
                        {
                            "id": 464,
                            "value": "productivity"
                        }
                    ]
                }
            ],
            "first_page_url": "/?page=1",
            "from": 1,
            "last_page": 4,
            "last_page_url": "/?page=4",
            "links": [
            ],
            "next_page_url": "/?page=2",
            "path": "/",
            "per_page": 20,
            "prev_page_url": null,
            "to": 20,
            "total": 78
        }
    }
}
```

---

---

### POST Login

Login an existing user

**Parameters**

|       Name | Required |  Type  | Description             |
| ---------: | :------: | :----: | ----------------------- |
|    `email` | required | string | user unique email name. |
| `password` | required | string | Minimum of 8 characters |

**Response**

```
// Sample Error
Status Code - 400
{
    "error": "invalid_credentials",
    "message": "Invalid Credentials"
}

// Sample Success
Status Code- 200
{
    "api_token": "YmY2MDE4Mzg0ZWY2ODI5YTliMjcwNjUyODNhNzQwNDEyN2UxODk1ZDEzOTc5YzVjY2FhMDNiNGQ3ZTk4MDg1MDgyNTFjZjRjMDNhOWRkYjc1MTA5Njk2ZjU3ZjNmOGFmMDVjMjVmYzM5ZWRhNWMwOTgwM2RkZDM0MzU5YjI1YTGn+iKGW5lyAS4oRduMGY90B/Qvz0PY1seLo1KAJoaLV4MjKQ+3V1Z/B4mMPBlqe/gkp+XMRcFaB/2v3QNcOskDmm1NYNVR01YcGiiYVZGHeVs40dBZkMHfrb+hJy+s/OGK8oIRroLrbONfhtsU4u+Qh6G4d5Fm5AQbB8fMDRxwOeN3ylDM431ys05DajpTIz8ZxLPfRTtsPo/TRnOwS8gwhVqDNNq+tntRJwWxGqh4de37PwRY1CBeYe0Rfq6RM8++yEZesTATy+6uZnQDzQ=="
}
```

---
