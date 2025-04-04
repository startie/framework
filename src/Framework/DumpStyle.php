<?php

declare(strict_types=1);

namespace Startie;

class DumpStyle
{
    /**
     * @psalm-suppress all
     * @source https://gist.github.com/sunel/5368b7b18a84829b06e4
     */
    public static string $color = "#005BFF";
    public static string $color2 = "#999";
    public static string $css = "
        margin-bottom: 18px;
        border: 1px solid #e1e1e8;
        padding: 8px;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border radius: 4px;
        display: block;
        white-space: pre-wrap;
        word-wrap: break-word;
        color: #333;
        font-family: Menlo, Monaco, Consolas, Courier New, monospace;
        margin-bottom: 0!important;
    ";

    //public static $icon_collapse = '⊟';
    public static string $icon_collapse
    = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAMAAADXT/YiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3MjlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzFDNDQyNEMyQzkxMUUxOTU4MEM4M0UxRDA0MUVGNSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzFDNDQyM0MyQzkxMUUxOTU4MEM4M0UxRDA0MUVGNSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3NDlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3MjlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PuF4AWkAAAA2UExURU9t2DBStczM/1h16DNmzHiW7iNFrypMvrnD52yJ4ezs7Onp6ejo6P///+Tk5GSG7D9h5SRGq0Q2K74AAAA/SURBVHjaLMhZDsAgDANRY3ZISnP/y1ZWeV+jAeuRSky6cKL4ryDdSggP8UC7r6GvR1YHxjazPQDmVzI/AQYAnFQDdVSJ80EAAAAASUVORK5CYII=';

    //public static $icon_expand = '⊞';
    public static string $icon_expand
    = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAMAAADXT/YiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3MTlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFQzZERTJDNEMyQzkxMUUxODRCQzgyRUNDMzZEQkZFQiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFQzZERTJDM0MyQzkxMUUxODRCQzgyRUNDMzZEQkZFQiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3MzlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3MTlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PkmDvWIAAABIUExURU9t2MzM/3iW7ubm59/f5urq85mZzOvr6////9ra38zMzObm5rfB8FZz5myJ4SNFrypMvjBStTNmzOvr+mSG7OXl8T9h5SRGq/OfqCEAAABKSURBVHjaFMlbEoAwCEPRULXF2jdW9r9T4czcyUdA4XWB0IgdNSybxU9amMzHzDlPKKu7Fd1e6+wY195jW0ARYZECxPq5Gn8BBgCr0gQmxpjKAwAAAABJRU5ErkJggg==';

    private static bool $hasArray = false;

    /**
     * @psalm-suppress all
     */
    public static function var_dump(
        mixed $var,
        bool $return = false,
        int $expandLevel = 1
    ): string {
        self::$hasArray = false;
        $toggScript =
            '
            var colToggle = function(toggID) 
            {
                var img = document.getElementById(toggID);
                if (document.getElementById(toggID + "-collapsable").style.display == "none") 
                {
                    document.getElementById(toggID + "-collapsable").style.display = "inline";
                    setImg(toggID, 0);
                    var previousSibling = document.getElementById(toggID + "-collapsable").previousSibling;
                    while (
                        previousSibling != null 
                        && 
                        (
                            previousSibling.nodeType != 1 
                            || 
                            previousSibling.tagName.toLowerCase() != "br"
                        )
                    ) {
                        previousSibling = previousSibling.previousSibling;
                    }
                } 
                else 
                {
                    document.getElementById(toggID + "-collapsable").style.display = "none";
                    setImg(toggID, 1);
                    var previousSibling = document.getElementById(toggID + "-collapsable").previousSibling; 
                    while (
                        previousSibling != null 
                        && 
                        (
                            previousSibling.nodeType != 1 
                            || 
                            previousSibling.tagName.toLowerCase() != "br"
                        )
                    ) {
                        previousSibling = previousSibling.previousSibling;}
                    }
                };
        ';

        $imgScript =
            '
            var setImg = function(objID,imgID,addStyle) {
                var imgStore = [
                    "data:image/png;base64,' . self::$icon_collapse . '", 
                    "data:image/png;base64,' . self::$icon_expand . '"
                ];
                if (objID) {
                    document.getElementById(objID).setAttribute("src", imgStore[imgID]);
                    if (addStyle){
                        document.getElementById(objID).setAttribute(
                        	"style", "position:relative;left:-5px;top:-1px;cursor:pointer;display:inline;"
                        );
                    }
                }
            };
        ';
        $jsCode = preg_replace('/ +/', ' ', '<script>' . $toggScript . $imgScript . '</script>');
        $html = "<pre style='" . self::$css . "'>";
        $done  = array();
        $html .= self::var_dump_plain($var, intval($expandLevel), 0, $done);
        $html .= '</pre>';
        if (self::$hasArray) {
            $html = $jsCode . $html;
        }
        if (!$return) {
            echo $html;
        }
        return $html;
    }

    /**
     * Display a variable's contents using nice HTML formatting (Without
     * the <pre> tag) and will properly display the values of variables
     * like booleans and resources. Supports collapsable arrays and objects
     * as well.
     *
     * @psalm-suppress all
     * @param mixed $var The variable to dump
     * @return string
     */
    public static function var_dump_plain(
        $var,
        int $expLevel,
        int $depth = 0,
        array $done = []
    ): string {
        $html = '';
        if ($expLevel > 0) {
            $expLevel--;
            $setImg = 0;
            $setStyle = 'display:inline;';
        } elseif ($expLevel == 0) {
            $setImg = 1;
            $setStyle = 'display:none;';
        } elseif ($expLevel < 0) {
            $setImg = 0;
            $setStyle = 'display:inline;';
        }
        if (is_bool($var)) {
            $html .= '
            	<span style="color:' . self::$color2 . ';">
            		bool (' . (($var) ? 'true' : 'false') . ')
            	</span>';
        } elseif (is_int($var)) {
            $html .= '<span style="color:' . self::$color2 . ';">int</span><span style="color:#999;">(</span><strong>' . $var . '</strong><span style="color:#999;">)</span>';
        } elseif (is_float($var)) {
            $html .= '<span style="color:' . self::$color2 . ';">float</span><span style="color:#999;">(</span><strong>' . $var . '</strong><span style="color:#999;">)</span>';
        } elseif (is_string($var)) {
            $html .= '<strong>"' . self::htmlentities($var) . '"</strong> <span style="color:' . self::$color2 . ';">string(' . strlen($var) . ')</span>';
        } elseif (is_null($var)) {
            $html .= '<strong>NULL</strong>';
        } elseif (is_resource($var)) {
            $html .= '<span style="color:' . self::$color2 . ';">resource</span>("' . get_resource_type($var) . '") <strong>"' . $var . '"</strong>';
        } elseif (is_array($var)) {
            // Check for recursion
            if ($depth > 0) {
                foreach ($done as $prev) {
                    if ($prev === $var) {
                        $html .= '<span style="color:' . self::$color . ';">array</span>(' . count($var) . ') *RECURSION DETECTED*';
                        return $html;
                    }
                }
                // Keep track of variables we have already processed to detect recursion
                $done[] = &$var;
            }
            self::$hasArray = true;
            $uuid = 'include-php-' . uniqid() . mt_rand(1, 1000000);
            $html .= (!empty($var) ? ' <img id="' . $uuid . '" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" onclick="javascript:colToggle(this.id);" /><script>setImg("' . $uuid . '",' . $setImg . ',1);</script>' : '') . '<span style="color:' . self::$color . ';">array</span>(' . count($var) . ')';
            if (!empty($var)) {
                $html .= ' <span id="' . $uuid . '-collapsable" style="' . $setStyle . '"><br />[<br />';
                $indent = 4;
                $longest_key = 0;
                foreach ($var as $key => $value) {
                    if (is_string($key)) {
                        $longest_key = max($longest_key, strlen($key) + 2);
                    } else {
                        $longest_key = max($longest_key, strlen((string) $key));
                    }
                }
                foreach ($var as $key => $value) {
                    if (is_numeric($key)) {
                        $html .= str_repeat(' ', $indent) . str_pad((string) $key, $longest_key, ' ');
                    } else {
                        $html .= str_repeat(' ', $indent) . str_pad('"' . self::htmlentities($key) . '"', $longest_key, ' ');
                    }
                    $html .= ' => ';
                    $value = explode('<br />', self::var_dump_plain($value, $expLevel, $depth + 1, $done));
                    foreach ($value as $line => $val) {
                        if ($line != 0) {
                            $value[$line] = str_repeat(' ', $indent * 2) . $val;
                        }
                    }
                    $html .= implode('<br />', $value) . '<br />';
                }
                $html .= ']</span>';
            }
        } elseif (is_object($var)) {
            // Check for recursion
            foreach ($done as $prev) {
                if ($prev === $var) {
                    $html .= '<span style="color:' . self::$color . ';">object</span>(' . get_class($var) . ') *RECURSION DETECTED*';
                    return $html;
                }
            }
            // Keep track of variables we have already processed to detect recursion
            $done[] = &$var;
            self::$hasArray = true;
            $uuid = 'include-php-' . uniqid() . mt_rand(1, 1000000);
            $html .= ' <img id="' . $uuid . '" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" onclick="javascript:colToggle(this.id);" /><script>setImg("' . $uuid . '",' . $setImg . ',1);</script><span style="color:' . self::$color . ';">object</span>(' . get_class($var) . ') <span id="' . $uuid . '-collapsable" style="' . $setStyle . '"><br />[<br />';
            $varArray = (array) $var;
            $indent = 4;
            $longest_key = 0;
            foreach ($varArray as $key => $value) {
                if (substr($key, 0, 2) == "\0*") {
                    unset($varArray[$key]);
                    $key = 'protected:' . substr($key, 2);
                    $varArray[$key] = $value;
                } elseif (substr($key, 0, 1) == "\0") {
                    unset($varArray[$key]);
                    $key = 'private:' . substr($key, 1, strpos(substr($key, 1), "\0")) . ':' . substr($key, strpos(substr($key, 1), "\0") + 1);
                    $varArray[$key] = $value;
                }
                if (is_string($key)) {
                    $longest_key = max($longest_key, strlen($key) + 2);
                } else {
                    $longest_key = max($longest_key, strlen((string) $key));
                }
            }
            foreach ($varArray as $key => $value) {
                if (is_numeric($key)) {
                    $html .= str_repeat(' ', $indent) . str_pad($key, $longest_key, ' ');
                } else {
                    $html .= str_repeat(' ', $indent) . str_pad('"' . self::htmlentities($key) . '"', $longest_key, ' ');
                }
                $html .= ' => ';
                $value = explode('<br />', self::var_dump_plain($value, $expLevel, $depth + 1, $done));
                foreach ($value as $line => $val) {
                    if ($line != 0) {
                        $value[$line] = str_repeat(' ', $indent * 2) . $val;
                    }
                }
                $html .= implode('<br />', $value) . '<br />';
            }
            $html .= ']</span>';
        }

        return $html;
    }

    /**
     * Convert entities, while preserving already-encoded entities.
     * 
     * @psalm-suppress all
     * @param string $string The text to be converted
     */
    public static function htmlentities(
        string $string,
        bool $preserve_encoded_entities = false
    ): string {
        if ($preserve_encoded_entities) {
            // @codeCoverageIgnoreStart
            if (defined('HHVM_VERSION')) {
                $translation_table = get_html_translation_table(
                    HTML_ENTITIES,
                    ENT_QUOTES
                );
            } else {
                $translation_table = get_html_translation_table(
                    HTML_ENTITIES,
                    ENT_QUOTES,
                    self::mbInternalEncoding()
                );
            }
            // @codeCoverageIgnoreEnd
            $translation_table[chr(38)] = '&';
            return preg_replace(
                '/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/',
                '&amp;',
                strtr($string, $translation_table)
            );
        }

        return htmlentities(
            $string,
            ENT_QUOTES,
            self::mbInternalEncoding()
        );
    }

    /**
     * Wrapper to prevent errors if the user doesn't have the mbstring
     * extension installed.
     * @psalm-suppress all
     * @param string $encoding
     */
    protected static function mbInternalEncoding(string|null $encoding = NULL): string|true
    {
        $encoding ??= "";

        if (function_exists('mb_internal_encoding')) {
            return $encoding !== ""
                ? mb_internal_encoding($encoding)
                : mb_internal_encoding();
        }
        // @codeCoverageIgnoreStart
        return 'UTF-8';
        // @codeCoverageIgnoreEnd
    }
}