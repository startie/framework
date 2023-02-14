# Installation

```
composer init
composer require startie/framework
```

# Set up

## Project structure in Terminal

```
mkdir backend && mkdir backend/Controllers && mkdir backend/Config && mkdir backend/Config/Bootstrap && mkdir backend/Routs && touch index.php && touch backend/Config/Bootstrap/Common.php && touch .env && mkdir backend/Config/Common && touch backend/Config/Common/App.php && touch backend/Controllers/Index_Controller.php && touch backend/Routs/Index.php
```

## `composer.json`

```json
"autoload": {
    "classmap": [
        "backend/Controllers",
    ]
}
```

## `index.php`

This is the file, that get recieves all requests and associate them with classes from 'Controllers' namespace.

```php
<?php

require 'backend/Config/Bootstrap/Common.php';
```

## `.env`

```php
# MAIN

STAGE="DEVELOPMENT"
MACHINE="LOCAL"

# MODE

POWER=1
MODE_DEV=1
NO_CONNECTION=0

# REGION

TIMEZONE="UTC"
DATE_DEFAULT_TIMEZONE="Europe/London"
DATE_TIMEZONE="Europe/London"
TIMEZONE_OFFSET="0"
LOCALE="ru_RU.UTF-8"

# NETWORK

PROTOCOL="http://"
SERVERNAME="localhost"
SERVERPORT=":8000"
DOMAIN="/"

# DIR

DIR_APP=""
DIR_APP_PHYSICAL=""
```

## `backend/Config/Bootstrap/Common.php`

```php
<?php

# BASIC

$root = dirname(__DIR__, 3);
require "$root/vendor/autoload.php";
\Startie\App::init($root);
\Startie\Config::init();

# ROUTING

\Startie\Router::init();
```

## `backend/Config/Common/App.php`

```php
<?php

const APP_NAME = "Startie";
const APP_DESCRIPTION = "Educational PHP framework";
const APP_FOUNDED = 2023;
const APP_V = "1.0.0";
const APP_V_DATE = "2023-12-01";
```

## `backend/Controllers/Index_Controller.php`

```php
<?php

namespace Controllers;

class Index_Controller
{
    public static function index()
    {
        echo "hi";
    }
}
```

## `backend/Routs/Index.php`

```php
<?php

return [
    '/' => [
        'title' => 'Index page',
        'controller' => 'Index::index',
    ],
];
```

# Run in Terminal

```
php -S localhost:8000
```
