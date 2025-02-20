<?php

namespace Startie;

class In
{
    /**
     * Get a 'variable' from global array and make optional modifications.
     * 
     * ```php
     * In::e('get', 'query', 'STR', ['', NULL], ['trim']);
     * ```
     * 
     * @param string $global A name of global variable: 'get' for $_GET, 'post' for $_POST, etc.
     * @param string $key A key name.
     * @param string $sanitizeType Type string for sanitizing.
     * @param array $if Array of 3 values: 0 – value for equality check; 1 – true case substitute; 2 – false case substitute
     * @param string[] $processing Functions to call on value.
     * @param array[] $replacements String replacements.
     */
    public static function e(
        string $global,
        string $key,
        string $sanitizeType,
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        /*
			fix name
		*/

        /*
			evaluate type
		*/

        if ($sanitizeType === '') {
            $sanitizeType = Input::$SanitizeTypeDefault;
        }

        $data = Input::$global($key, $sanitizeType);

        /* if */

        if (!empty($if)) {
            $conditionValue = $if[0];
            $trueCaseValue = $if[1];
            $falseCaseValue = $if[2] ?? null;
            if ($data === $conditionValue) {
                $data = $trueCaseValue;
            } else {
                if (!is_null($falseCaseValue)) {
                    $data = $falseCaseValue;
                }
            }
        }

        /* processing */

        if (!empty($processing) && $data) {
            foreach ($processing as $f) {
                $data = call_user_func($f, $data);
            }
        }

        /* replacements */

        if (!empty($replacements)) {
            foreach ($replacements as $replacement) {
                if (isset($replacement[0]) && isset($replacement[1])) {
                    preg_replace(
                        "/" . preg_quote($replacement[0]) . "/",
                        $replacement[1],
                        $data ?? []
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Short-hand method
     */
    public static function cookie(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "cookie",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function env(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "env",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function files(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "files",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function get(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "get",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function post(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "post",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function request(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "request",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function server(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "server",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }

    /**
     * Short-hand method
     */
    public static function session(
        string $key,
        string $sanitizeType = '',
        array $if = [],
        array $processing = [],
        array $replacements = []
    ): mixed {
        return In::e(
            "session",
            $key,
            $sanitizeType,
            $if,
            $processing,
            $replacements
        );
    }
}