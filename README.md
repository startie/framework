# Getting Started

## 1. Install package

```
composer require startie/framework
```

## 2. Create index file for web-server

It will be the file that will recieve all client requests and associate them with controllers.

```
touch index.php
```

```php
<?php

require 'backend/Config/Bootstrap/Common.php';
```

## 3. Create main bootstrap file

This file initializes all important components.

```
mkdir backend/Config/Bootstrap
touch backend/Config/Bootstrap/Common.php
```

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

## 4. Create main development directory

```
mkdir backend
```

## 5. Create config directory

```
mkdir backend/Config
mkdir backend/Config/Common
```

```
touch backend/Config/Common/App.php
```

```php
<?php

const APP_NAME = "Startie";
const APP_DESCRIPTION = "Educational PHP framework";
const APP_FOUNDED = 2023;
const APP_V = "";
const APP_V_DATE = "";
```

## 6. Create enviroment config file

```
touch .env
```

```env
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

DIR_APP="" # full path to the root directory with trailing slash
```

## 7. Create main controller

```
mkdir backend/Controllers
touch backend/Controllers/Index_Controller.php
```

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

## 8. Modify `composer.json`

To autoload all controllers's classes.

```json
"autoload": {
    "classmap": [
        "backend/Controllers"
    ]
}
```

## 9. Create routs

```
mkdir backend/Routs
touch backend/Routs/Index.php
```

```php
<?php

return [
    '/' => [
        'title' => 'Index page',
        'controller' => 'Index::index',
    ],
];
```

## 10. Run server

```
php -S localhost:8000
```
