# PHP client for Shopware 6 Store API

## Installation
```bash
composer require yireo/shopware6-store-api-client
```

## Usage
```php
use Yireo\StoreApiClient\Client;

require_once __DIR__ . '/vendor/autoload.php';

$apiClient = new Client('http://example.com/', 'YOUR_ACCESS_KEY');
print_r($apiClient->getToken());
```
