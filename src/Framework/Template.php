<?php

namespace Startie;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Startie\Input;

class Template
{
    public static function r($templatePath, $data, $csrf = null)
    {
        #
        #   Initialize Mustache

        $mustache = new Mustache_Engine(array(
            'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS],
            'loader' => new Mustache_Loader_FilesystemLoader(BACKEND_DIR . "Templates/", [
                'extension' => '.mst'
            ]),
            'escape' => function ($value) {
                if (is_array($value)) {
                    $value = var_export($value, true);
                    throw new \Startie\Exception(
                        "Value passed to the template can't be an array: $value"
                    );
                }
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            },
        ));

        #
        #   Csrf protection

        if ($csrf == 'csrf') {

            # Get current url

            if (Input::is('POST', 'csrfUrl')) {
                $url = Input::post('csrfUrl', 'STR');
                $url = str_replace(Url::app(), "", $url);
            } else {
                $url = Input::get('url', 'STR');
            }

            # Get configs for this url

            $configs = $_SESSION['csrf'][$url];

            # Get id of last config

            $cnt = count($configs) - 1;

            # Get config and assign its value to 'csrfToken' field in form

            $data['csrfToken'] = $configs[$cnt]['token'];

            if (Input::is('POST', 'csrfUrl')) {
                $data['csrfUrl'] = $url;
            }
        }

        #
        #   Render

        $template = $mustache->loadTemplate($templatePath);
        return $template->render($data);
    }

    public static function render($templatePath, $data, $csrf = null)
    {
        echo self::r($templatePath, $data, $csrf);
    }
}
