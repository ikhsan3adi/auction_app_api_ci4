# Online Auction Api

[![PHP Composer](https://github.com/x4nn/ci4_online_auction_api/actions/workflows/php.yml/badge.svg)](https://github.com/x4nn/ci4_online_auction_api/actions/workflows/php.yml)

 REST API for [flutter_online_auction_app](https://github.com/x4nn/flutter_online_auction_app) using Codeigniter 4


## Requirement

- Composer
- PHP 8.0+, MySQL or XAMPP

## Configuration

In your application, perform the following setup: 
1.  Rename `env` file to `.env`.
2.  Set the jwt secretkey and token expiration in the `.env` file if you want to change them.
3.  Use composer to manage your dependencies and download PHP-JWT:
```shell
    composer require firebase/php-jwt
```
4.  Ensure your database is setup correctly, then run the migrations: 
```shell
    php spark migrate -all  
```


## API Reference

#### Login

```http
  POST /api/login
```

| Parameter  | Type     | Description                 |
| :--------  | :------- | :-------------------------  |
| `username` | `string` | **Required**. Your username |
| `password` | `string` | **Required**. Your password |


#### Register user

```http
  POST /api/user
```

| Parameter      | Type     | Description    |
| :--------      | :------- | :--------------|
| `username`     | `string` | **Required**.  |
| `password`     | `string` | **Required**.  |
| `name`         | `string` | **Required**.  |
| `email`        | `string` | **Required**.  |
| `phone`        | `string` |                |


#### Get all item

Restricted based on user id from authentication.

```http
  GET /api/item
```

#### Get all auction

```http
  GET /api/auction
```

And many more..
## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

## Authors

- [@x4nn](https://www.github.com/x4nn)
