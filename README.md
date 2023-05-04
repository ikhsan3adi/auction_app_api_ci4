# Online Auction Api

[![PHP Composer](https://github.com/x4nn/ci4_online_auction_api/actions/workflows/php.yml/badge.svg)](https://github.com/x4nn/ci4_online_auction_api/actions/workflows/php.yml)

 REST API for [Auction App Flutter](https://github.com/ikhsan3adi/AuctionApp) using Codeigniter 4


## Requirement

- Composer
- PHP 8.0+, MySQL or XAMPP with `-intl` extension enable

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

```
POST   /api/login
```

| Parameter  | Type     | Description                 |
| :--------  | :------- | :-------------------------  |
| `username` | `string` | **Required**. Your username |
| `password` | `string` | **Required**. Your password |


#### Register user

```
POST   /api/user
```

| Parameter      | Type     | Description    |
| :--------      | :------- | :--------------|
| `username`     | `string` | **Required**.  |
| `password`     | `string` | **Required**.  |
| `name`         | `string` | **Required**.  |
| `email`        | `string` | **Required**.  |
| `phone`        | `string` |                |

---
Warning:warning: Make sure to add authorization header

#### Get auction

```
GET    /api/auction
```
```
GET    /api/auction/{id}
```
#### Get user/my auctions
```
GET    /api/user/auction
```
#### Get auction bids
```
GET    /api/auction/{id}/bids
```
---
#### Create an auction
```
POST   /api/auction
```
#### Update an auction
```
PATCH  /api/auction/{id}
```
#### Set winner & close auction
```
PATCH  /api/auction/{id}/winner
```
```
PATCH  /api/auction/{id}/close
```
#### Delete an auction
```
DELETE /api/auction/{id}
```
#### Get auction history (closed auction)
```
GET    /api/auction/history
```
```
GET    /api/auction/history/{id}
```
---
#### Get bid
```
GET    /api/bid
```
```
GET    /api/bid/{id}
```
#### Get user/my bids
```
GET    /api/user/bid
```
---
#### Create/place a bid
```
POST   /api/bid
```
#### Update bid
```
PATCH  /api/bid/{id}
```
#### Delete bid
```
DELETE /api/bid/{id}
```
---
#### Get item
```
GET    /api/item
```
```
GET    /api/item/{id}
```
#### Create an item
```
POST   /api/item
```
#### Update an item
```
PATCH  /api/item/{id}
```
#### Delete an item
```
DELETE /api/item/{id}
```
---
#### Get user
```
GET    /api/user
```
```
GET    /api/user/{id}
```
#### Update user
```
PATCH  /api/user/{id}
```
#### Delete user
```
DELETE /api/user/{id}
```
Still need more improvement
## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

## Authors

- [@ikhsan3adi](https://www.github.com/ikhsan3adi)

## Contributors

- [@asyncguy](https://www.github.com/asyncguy)
