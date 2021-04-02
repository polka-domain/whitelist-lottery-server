# Whitelist Lottery Server

## API

### 1. Submit user's information

- **Request Path**: /api/users
- **Request Method**: POST
- **Request Parameters**:

| key | type | comment |
| --- | --- | --- |
| eth_address | string | Ethereum address of user |
| email | string | email of user |
| twitter | string | twitter handler of user |
| telegram | string | telegram username of user |
| domain | string | desire domain of user |
| sign | string | signature of Ethereum address signed by user's wallet |
   
### 2. Query user
- **Request Path**: /api/users/:eth_address
- **Request Method**: GET

| key | type | comment |
| --- | --- | --- |
| eth_address | string | Ethereum address of user |

## Error handle
### Error response
| key | type | comment |
| --- | --- | --- |
| error | string | error code |
| msg | string/object | error message |

### Error status
##### 400 Bad Request
``` json
{
  "error": "invalid_ethereum_address",
}
```

``` json
{
  "error": "invalid_signature",
}
```

##### 404 Not Found
``` json
{
  "error": "resource_not_found",
}
```

##### 422 Unprocessable Entity
``` json
{
  "error": {
      "eth_address": [
        "The eth address field is required."
      ],
      "email": [
        "The email field is required."
      ],
      "twitter": [
        "The twitter field is required."
      ],
      "telegram": [
        "The telegram field is required."
      ],
      "domain": [
        "The domain field is required."
      ],
      "sign": [
        "The sign field is required."
      ]
    }
}
```

##### 500 Internal Server Error
``` json
{
  "error": "internal_server_error",
}
```
