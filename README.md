## PHP Google Sheets API Examples

Examples of Google Sheets API implementations using PHP

## Requirements

1. [PHP](https://php.net) 7.4+
2. a single file named `credentials.json` from [Google](https://console.cloud.google.com/)

## Installation

### Using [Composer](https://getcomposer.org)

To install, just add the following script to your `composer.json` file:

```json
{
    "require": {
        "php": ">=8.0.0",
        "dannsbass/google-sheets": "*"
    },
    "scripts": {
        "pre-autoload-dump": "Google\\Task\\Composer::cleanup"
    },
    "extra": {
        "google/apiclient-services": [
            "Sheets"
        ]
    }
}
```

or by running the following command:

```shell
composer require dannsbass/google-sheets
```

Composer installs autoloader at `./vendor/autoloader.php`. to include the library in your script, add:

```php
require_once 'vendor/autoload.php';
```

### Install from source

Download this library from Github:

```shell
git clone https://github.com/dannsbass/google-sheets
cd google-sheets
```

Then include `src/Dannsheet.php` in your script:

```php
require_once 'src/Dannsheet.php';
```

## Usage

### Creating a simple configuration
```php
<?php

require_once './src/Dannsheet.php';

Dannsheet::setCredentials(__DIR__ . '/path/to/credentials.json');
Dannsheet::setSpreasheetId(YOUR_SPREADSHEET_ID); // from Google

```
See `examples/` directory for more details.