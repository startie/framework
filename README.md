### Install

```
composer init
composer require startie/framework
```

### Set up folders

```
mkdir public
mkdir backend
mkdir backend/Controllers
mkdir backend/Models
```

### /composer.json

```
"autoload": {
    "classmap": [
        "backend/Controllers",
        "backend/Models",
    ]
},
```

### Config

```
mkdir backend/Config
mkdir backend/Config/Bootstrap
```

### index.php

```
touch index.php
```

```php
<?php

require 'backend/Config/Bootstrap/Common.php';
```

### .env

```
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

PROTOCOL=""
SERVERNAME=""
SERVERPORT=""
DOMAIN=""

DIR_APP=""
DIR_APP_PHYSICAL=""

INPUT_TYPE_DEFAULT="STR"
```

### /backend/Config/Bootstrap/Common.php

```
<?php

$root = dirname(__DIR__, 3);

require "$root/vendor/autoload.php";

$root = dirname(__DIR__, 3);
\Startie\App::init($root);

\Startie\Config::init();
\Startie\Router::init();

```

### /backend/Config/Common.php

```
<?php

$APP_NAME = "";
$APP_DESCRIPTION = "";
$APP_FOUNDED = 2022;
$APP_V = "";
$APP_V_DATE = "";
```

### /backend/Controllers/Index_Controller.php

```
<?php

class Index_Controller
{
    public static function index()
    {
        echo "hello world";
    }
}
```

### /backend/Routs/Index.php

```
<?php

return $Index = [
    '/' => [
        'title' => 'Index page',
        'controller' => 'Index::index',
    ],
];
```
