<?php

namespace Startie;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

use Startie\Input;
use Startie\Url;

class Template
{
    public static function return(
        string $templatePath,
        array $data,
        string $csrf = null
    ): string {
        /*
            Initialize Mustache engine
        */

        $mustache = new Mustache_Engine([
            'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS],
            'loader' => new Mustache_Loader_FilesystemLoader(
                BACKEND_DIR . "Templates/",
                [
                    'extension' => '.mst'
                ]
            ),
            'escape' => function ($value) use ($templatePath, $data) {
                if (is_array($value)) {
                    $valueAsExport = var_export($value, true);
                    throw new \Startie\Exception(
                        "One of the values passed to the template `$templatePath`"
                            . " is an array, but expected a string."
                    );
                }
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            },
        ]);

        /*
            CSRF protection
        */

        if ($csrf === 'csrf') {
            // Get current url
            if (Input::is('POST', 'csrfUrl')) {
                $url = Input::post('csrfUrl', 'STR');
                $url = str_replace(Url::app(), "", $url);
            } else {
                $url = Input::get('url', 'STR');
            }

            // Get configs for this url
            $configs = $_SESSION['csrf'][$url];

            // Get id of last config
            $cnt = count($configs) - 1;

            // Get config and assign its value to 'csrfToken' field in form
            $data['csrfToken'] = $configs[$cnt]['token'];

            if (Input::is('POST', 'csrfUrl')) {
                $data['csrfUrl'] = $url;
            }
        }

        /*
            Texts
        */

        global $t;
        $data['t'] = $t;

        /*
            Render
        */

        $template = $mustache->loadTemplate($templatePath);
        return $template->render($data);
    }

    /**
     * Synonym for `Template::return()`
     * 
     * @deprecated Use helper `template()`
     */
    public static function r(
        string $templatePath,
        array $data,
        string|null $csrf = null
    ): string {
        return self::return($templatePath, $data, $csrf);
    }

    /**
     * Shorthand for `Template::return()`
     * 
     * @deprecated Use helper `template()`
     */
    public static function render(
        string $templatePath,
        array $data,
        string|null $csrf = null
    ): void {
        echo self::return($templatePath, $data, $csrf);
    }
}