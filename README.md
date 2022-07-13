# Cookier

<p align="center">
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-lightgrey.svg" alt="License"></a>
<a href="https://packagist.org/packages/attla/cookier"><img src="https://img.shields.io/packagist/v/attla/cookier" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/attla/cookier"><img src="https://img.shields.io/packagist/dt/attla/cookier" alt="Total Downloads"></a>
</p>

🍪 Powerful wrapper for improved cookie integration on Laravel.

## Installation

```bash
composer require attla/cookier
```

## Usage

This wrapper use same functionality as the Laravel cookie facade but a little incremented.

```php

// on setting a cookie you can retrieve the cookie while set it
$httpCookie = \Cookier::set('user', 'nicolau');

// retrieve the cookie from request
$user = \Cookier::get('user', 'default');

// forget a cookie
\Cookier::forget('user');

// check if the cookie has queued
\Cookier::hasQueued('user');

// getting many cookies
$cookies = \Cookier::getMany([
    'user' => 'default',
    'password',
]);

```

All methods available on laravel cookie facade can be used.

## License

This package is licensed under the [MIT license](LICENSE) © [Octha](https://octha.com).
