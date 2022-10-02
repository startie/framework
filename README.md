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

```php
<?php

require 'backend/Config/Bootstrap/Common.php';
```

## `.env`

```php
POWER=1
MODE_DEV=1
NO_CONNECTION=0

STAGE="DEVELOPMENT"
MACHINE="LOCAL"

TIMEZONE="UTC"
DATE_DEFAULT_TIMEZONE="Europe/London"
DATE_TIMEZONE="Europe/London"
TIMEZONE_OFFSET="0"
LOCALE="ru_RU.UTF-8"

PROTOCOL="http://"
SERVERNAME="localhost"
SERVERPORT=":8000"
DOMAIN="/"

DIR_APP=""
DIR_APP_PHYSICAL=""
```

## `backend/Config/Bootstrap/Common.php`

```php
<?php

$root = dirname(__DIR__, 3);

require "$root/vendor/autoload.php";

\Startie\App::init($root);
\Startie\Config::init();
\Startie\Router::init();
```

## `backend/Config/Common/App.php`

```php
<?php

$APP_NAME = "";
$APP_DESCRIPTION = "";
$APP_FOUNDED = 2022;
$APP_V = "";
$APP_V_DATE = "";
```

## `backend/Controllers/Index_Controller.php`

```php
<?php

namespace Controllers;

class Index_Controller
{
    public static function index()
    {
        echo "Index";
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
