<?php

namespace Startie;

class StatementParamValidator
{
    public static function select(array $param): void
    {
        if (!array_is_list($param)) {
            throw new Exception(
                'The `$select` param should be list array'
            );
        };
    }

    public static function join(array $params)
    {
        foreach ($params as $table => $rulesArray) {
            if (!is_array($rulesArray)) {
                throw new Exception(
                    'Params of `$join` should be an array'
                );
            } else {
                if (count($rulesArray) < 2) {
                    throw new Exception(
                        'The count of params in `$join` cannot be less than 2'
                    );
                } elseif (count($rulesArray) > 3) {
                    throw new Exception(
                        'The count of params in `$join` cannot be more than 3'
                    );
                }
            }
        }

        return true;
    }

    public static function where(array $params)
    {
        foreach ($params as $column => $values) {
            /*
                Protection from:

                ```
                'where' => [
                    '<TABLE>.<COLUMN>' => 100
                ]
                ```

                100 is considered as $values
            */
            if (!is_array($values)) {
                throw new Exception(
                    '`$values` should be an array'
                );
            }

            /*
                Protection from:

                ```
                'where' => [
                    '<TABLE>.<COLUMN>' => [],
                ]
                ```

                [] is considered as $values
            */
            if (count($values) === 0) {
                throw new Exception(
                    '`$values` should have at least 1 array'
                );
            }

            /*
                Protection from:

                ```
                'where' => [
                    '<TABLE>.<COLUMN>' => [
                        '<OPERATOR> <VALUE>', 
                        '<TYPE>', 
                        '<MODIFICATOR>'
                    ],
                ]
                ```
            */
            $firstValue = $values[0];

            if (!is_array($firstValue)) {
                throw new Exception(
                    'First element of `$values` should be an array'
                );
            };

            /*
                Protection from:

                'where' => [
                    '<TABLE>.<COLUMN>' => [
                        [[], [], []],
                    ],
                    // ...
                ]

                $values is array of arrays
                $value is array
            */
            foreach ($values as $value) {
                foreach ($value as $part) {
                    if (!is_string($part)) {
                        throw new Exception(
                            'Part should be a string'
                        );
                    };
                };
            };
        }
    }
}