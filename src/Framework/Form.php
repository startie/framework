<?php

namespace Startie;

/*

	Examples:
	
	Form::start();
		Form::text('z', Input::get('z', 'STR'));
		Form::submit();
	Form::end();
 */

class Form
{
    public static function open(string $method = '', string $action = ''): void
    {
        echo "<form method='$method' action='$action'>";
    }

    public static function text(string $name, string $value): void
    {
        echo "<input name='$name' value='$value'>";
    }

    public static function submit(): void
    {
        echo "<input type='submit'>";
    }

    public static function end(): void
    {
        echo "</form>";
    }

    public static function hiddenQueryParams(
        array $exceptIndexes = []
    ): array {
        $params = Url::getQueryParams();
        unset($params['url']);

        # Clear another
        if (!empty($exceptIndexes)) {
            foreach ($exceptIndexes as $exceptIndex) {
                unset($params[$exceptIndex]);
            }
        }

        # Build in format
        $newParams = [];
        foreach ($params as $i => &$param) {
            if (is_array($param)) {
                $newParam = [];
                $newParam['name'] = $i;
                $newParam['value'] = $param[0];
                $newParams[] = $newParam;
                unset($params[$i]);
            }
        }

        return $newParams;
    }

    public static function renderQueryParamsAsHidden(
        array $exceptIndexes = []
    ): void {
        $hiddenParams = self::hiddenQueryParams($exceptIndexes);
        foreach ($hiddenParams as $param) {
            $name = $param['name'];
            $value = $param['value'];
            echo "<input type='hidden' value='{$value}' name='{$name}'>";
        }
    }
}