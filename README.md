# Flutter Auction App RESTful API Service

[![PHP Composer](https://github.com/ikhsan3adi/ci4_online_auction_api/actions/workflows/php.yml/badge.svg)](https://github.com/ikhsan3adi/ci4_online_auction_api/actions/workflows/php.yml)

 REST API for [Flutter Auction App](https://github.com/ikhsan3adi/Flutter-Auction-App) using Codeigniter 4


## Requirement

- Composer
- PHP 8.0+, MySQL or XAMPP with `-intl` extension enable

## Configuration

1.  Rename `env` file to `.env`.
2.  Set the jwt secretkey and token expiration in the `.env` file if you want to change them.
3.  Install dependencies:
```shell
    composer install
```
4.  Ensure your database is setup correctly, then run the migrations: 
```shell
    php spark migrate -all  
```

## API Reference

#### Login

```
POST   /api/login
```

| Parameter  | Type     | Description                 |
| :--------  | :------- | :-------------------------  |
| `username` | `string` | **Required**. Your username |
| `password` | `string` | **Required**. Your password |


#### Register user

```
POST   /api/users
```

| Parameter      | Type     | Description    |
| :--------      | :------- | :--------------|
| `username`     | `string` | **Required**.  |
| `password`     | `string` | **Required**.  |
| `name`         | `string` | **Required**.  |
| `email`        | `string` | **Required**.  |
| `phone`        | `string` |                |

---
Warning :warning: Make sure to add authorization header (Bearer token)

#### Get auction

```
GET    /api/auctions
```
```
GET    /api/auctions/{id}
```
#### Get user/my auctions
```
GET    /api/users/auctions
```
#### Get auction bids
```
GET    /api/auctions/{id}/bids
```
---
#### Create an auction
```
POST   /api/auctions
```
#### Update an auction
```
PATCH  /api/auctions/{id}
```
#### Set winner & close auction
```
PATCH  /api/auctions/{id}/winner
```
```
PATCH  /api/auctions/{id}/close
```
#### Delete an auction
```
DELETE /api/auctions/{id}
```
---
#### Get bid
```
GET    /api/bids
```
```
GET    /api/bids/{id}
```
#### Get user/my bids
```
GET    /api/users/bids
```
---
#### Create/place a bid
```
POST   /api/bids
```
#### Update bid
```
PATCH  /api/bids/{id}
```
#### Delete bid
```
DELETE /api/bids/{id}
```
---
#### Get item
```
GET    /api/items
```
```
GET    /api/items/{id}
```
#### Create an item
```
POST   /api/items
```
#### Update an item
```
PATCH  /api/items/{id}
```
#### Delete an item
```
DELETE /api/items{id}
```
---
#### Get user
```
GET    /api/users
```
```
GET    /api/users/{id}
```
#### Update user
```
PATCH  /api/users/{id}
```
#### Delete user
```
DELETE /api/users/{id}
```

Still need more improvement

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

## Donation

[![Donate paypal](https://img.shields.io/badge/Donate-PayPal-green.svg?style=for-the-badge)](https://paypal.me/xannxett?country.x=ID&locale.x=en_US)
[![Donate saweria](https://img.shields.io/badge/Donate-Saweria-red?style=for-the-badge&link=https%3A%2F%2Fsaweria.co%2Fxiboxann)](https://saweria.co/xiboxann)

## License

![GitHub license](https://img.shields.io/github/license/ikhsan3adi/ci4_online_auction_api?style=for-the-badge)

## Authors

- [@ikhsan3adi](https://www.github.com/ikhsan3adi)
