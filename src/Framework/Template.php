<?php

namespace Startie;

class Template
{
    public static function r($template, $data, $csrf = null)
    {
        if (is_array($data)) {
            $options =  array(
                'extension' => '.mst',
            );

            $m = new Mustache_Engine(array(
                'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS],
                'loader' => new Mustache_Loader_FilesystemLoader(BACKEND_TEMPLATES_DIR, $options),
            ));

            # Csrf protection is 'on'
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

            return $m->render($template, $data);
        }
    }

    public static function render($template, $data, $csrf = null)
    {
        echo self::r($template, $data, $csrf);
    }
}
