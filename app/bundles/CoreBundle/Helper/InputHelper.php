<?php

namespace Mautic\CoreBundle\Helper;

use Joomla\Filter\InputFilter;

class InputHelper
{
    /**
     * String filter.
     */
    private static ?\Joomla\Filter\InputFilter $stringFilter = null;

    /**
     * HTML filter.
     */
    private static ?\Joomla\Filter\InputFilter $htmlFilter = null;

    private static ?\Joomla\Filter\InputFilter $strictHtmlFilter = null;

    /**
     * Adjust the boolean values from text to boolean.
     * Do not convert null to false.
     * Do not convert invalid values to false, but return null.
     *
     * @param bool|int|string|null $value
     *
     * @return bool|null
     */
    public static function boolean($value)
    {
        return match (strtoupper((string) $value)) {
            'T', 'Y' => true,
            'F', 'N' => false,
            default => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        };
    }

    /**
     * @param bool $html
     * @param bool $strict
     *
     * @return InputFilter
     */
    private static function getFilter($html = false, $strict = false)
    {
        if (empty(self::$htmlFilter)) {
            // Most of Mautic's HTML uses include full HTML documents so use blacklist method
            self::$htmlFilter               = new InputFilter([], [], 1, 1);
            self::$htmlFilter->tagBlacklist = [
                'applet',
                'bgsound',
                'base',
                'basefont',
                'embed',
                'frame',
                'frameset',
                'ilayer',
                'layer',
                'object',
            ];

            self::$htmlFilter->attrBlacklist = [
                'codebase',
                'dynsrc',
                'lowsrc',
            ];

            // Strict HTML - basic one liner formating really
            self::$strictHtmlFilter = new InputFilter(
                [
                    'b',
                    'i',
                    'u',
                    'em',
                    'strong',
                    'a',
                    'span',
                ], [], 0, 1);

            self::$strictHtmlFilter->attrBlacklist = [
                'codebase',
                'dynsrc',
                'lowsrc',
            ];

            // Standard behavior if HTML is not specifically used
            self::$stringFilter = new InputFilter();
        }

        return match (true) {
            $html => ($strict) ? self::$strictHtmlFilter : self::$htmlFilter,
            default => self::$stringFilter,
        };
    }

    /**
     * Wrapper to InputHelper.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::getFilter()->clean($arguments[0], $name);
    }

    /**
     * Wrapper function to clean inputs.  $mask can be an array of keys as the field names and values as the cleaning
     * function to be used for the specific field.
     *
     * @param bool  $urldecode
     *
     * @return mixed
     */
    public static function _(mixed $value, mixed $mask = 'clean', $urldecode = false)
    {
        if (is_array($value)) {
            foreach ($value as $k => &$v) {
                $useMask = 'filter';
                if (is_array($mask)) {
                    if (array_key_exists($k, $mask)) {
                        if (is_array($mask[$k])) {
                            $useMask = $mask[$k];
                        } elseif (method_exists(\Mautic\CoreBundle\Helper\InputHelper::class, $mask[$k])) {
                            $useMask = $mask[$k];
                        }
                    } elseif (is_array($v)) {
                        // Likely a collection so use the same mask
                        $useMask = $mask;
                    }
                } elseif (method_exists(\Mautic\CoreBundle\Helper\InputHelper::class, $mask)) {
                    $useMask = $mask;
                }

                if (is_array($v)) {
                    $v = self::_($v, $useMask, $urldecode);
                } elseif ('filter' == $useMask) {
                    $v = self::getFilter()->clean($v, $useMask);
                } else {
                    $v = self::$useMask($v, $urldecode);
                }
            }

            return $value;
        } elseif (is_string($mask) && method_exists(\Mautic\CoreBundle\Helper\InputHelper::class, $mask)) {
            return self::$mask($value, $urldecode);
        } else {
            return self::getFilter()->clean($value, $mask);
        }
    }

    /**
     * Cleans value by HTML-escaping '"<>& and characters with ASCII value less than 32.
     *
     * @param            $value
     *
     * @return mixed|string
     */
    public static function clean($value, bool $urldecode = false)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = self::clean($v, $urldecode);
            }

            return $value;
        } elseif ($urldecode) {
            $value = urldecode($value);
        }

        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Strips tags.
     *
     * @param            $value
     *
     * @return mixed
     */
    public static function string($value, bool $urldecode = false)
    {
        if ($urldecode) {
            $value = urldecode($value);
        }

        return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    }

    /**
     * Strips non-alphanumeric characters.
     *
     * @param            $value
     * @param array      $allowedCharacters
     *
     * @return string
     */
    public static function alphanum($value, bool $urldecode = false, bool $convertSpacesTo = false, $allowedCharacters = [])
    {
        if ($urldecode) {
            $value = urldecode($value);
        }

        if ($convertSpacesTo) {
            $value               = str_replace(' ', $convertSpacesTo, $value);
            $allowedCharacters[] = $convertSpacesTo;
        }

        $delimiter = '~';
        if (false && in_array($delimiter, $allowedCharacters)) {
            $delimiter = '#';
        }

        if (!empty($allowedCharacters)) {
            $regex = $delimiter.'[^0-9a-z'.preg_quote(implode('', $allowedCharacters)).']+'.$delimiter.'i';
        } else {
            $regex = $delimiter.'[^0-9a-z]+'.$delimiter.'i';
        }

        return trim(preg_replace($regex, '', $value));
    }

    /**
     * Returns a satnitized string which can be used in a file system.
     * Attaches the file extension if provided.
     *
     * @param string $value
     * @param string $extension
     *
     * @return string
     */
    public static function filename($value, $extension = null)
    {
        $value = str_replace(' ', '_', $value);

        $sanitized = preg_replace("/[^a-z0-9\.\_-]/", '', strtolower($value));
        $sanitized = preg_replace("/^\.\./", '', strtolower($sanitized));

        if (null === $extension) {
            return $sanitized;
        }

        return sprintf('%s.%s', $sanitized, $extension);
    }

    /**
     * Returns raw value.
     *
     * @param            $value
     *
     * @return string
     */
    public static function raw($value, bool $urldecode = false)
    {
        if ($urldecode) {
            $value = urldecode($value);
        }

        return $value;
    }

    /**
     * Removes all characters except those allowed in URLs.
     *
     * @param            $value
     * @param null       $allowedProtocols
     * @param null       $defaultProtocol
     * @param array      $removeQuery
     *
     * @return mixed|string
     */
    public static function url($value, bool $urldecode = false, $allowedProtocols = null, $defaultProtocol = null, $removeQuery = [], bool $ignoreFragment = false)
    {
        if ($urldecode) {
            $value = urldecode($value);
        }

        if (empty($allowedProtocols)) {
            $allowedProtocols = ['https', 'http', 'ftp'];
        }
        if (empty($defaultProtocol)) {
            $defaultProtocol = 'http';
        }

        $value = filter_var($value, FILTER_SANITIZE_URL);
        $parts = parse_url($value);

        if (!$parts || !filter_var($value, FILTER_VALIDATE_URL)) {
            // This is a bad URL so just clean the whole thing and return it
            return self::clean($value);
        }

        $parts['scheme'] ??= $defaultProtocol;
        if (!in_array($parts['scheme'], $allowedProtocols)) {
            $parts['scheme'] = $defaultProtocol;
        }

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);

            // remove specified keys from the query
            foreach ($removeQuery as $q) {
                if (isset($query[$q])) {
                    unset($query[$q]);
                }
            }

            // http_build_query urlencodes by default
            $parts['query'] = http_build_query($query);
        }

        return
            // already clean due to the exclusion list above
            (!empty($parts['scheme']) ? $parts['scheme'].'://' : '').
            // strip tags that could be embedded in the username or password
            (!empty($parts['user']) ? strip_tags($parts['user']).':' : '').
            (!empty($parts['pass']) ? strip_tags($parts['pass']).'@' : '').
            // should be caught by FILTER_VALIDATE_URL if the host has invalid characters
            (!empty($parts['host']) ? $parts['host'] : '').
            // type cast to int
            (!empty($parts['port']) ? ':'.(int) $parts['port'] : '').
            // strip tags that could be embedded in a path
            (!empty($parts['path']) ? strip_tags($parts['path']) : '').
            // cleaned through the parse_str (urldecode) and http_build_query (urlencode) above
            (!empty($parts['query']) ? '?'.$parts['query'] : '').
            // strip tags that could be embedded in the fragment
            (!$ignoreFragment && !empty($parts['fragment']) ? '#'.strip_tags($parts['fragment']) : '');
    }

    /**
     * Removes all characters except those allowed in emails.
     *
     * @param            $value
     *
     * @return mixed
     */
    public static function email($value, bool $urldecode = false)
    {
        if ($urldecode) {
            $value = urldecode($value);
        }

        $value = substr($value, 0, 254);
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);

        return trim($value);
    }

    /**
     * Returns a clean array.
     *
     * @param            $value
     *
     * @return array|mixed|string
     */
    public static function cleanArray($value, bool $urldecode = false)
    {
        $value = self::clean($value, $urldecode);

        // Return empty array for empty values
        if (empty($value)) {
            return [];
        }

        // Put a value into array if not an array
        if (!is_array($value)) {
            $value = [$value];
        }

        return $value;
    }

    /**
     * Returns clean HTML.
     *
     * @param $value
     *
     * @return mixed|string
     */
    public static function html($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = self::html($val);
            }
        } else {
            // Special handling for doctype
            $doctypeFound = preg_match('/(<!DOCTYPE(.*?)>)/is', $value, $doctype);
            // Special handling for CDATA tags
            $value = str_replace(['<![CDATA[', ']]>'], ['<mcdata>', '</mcdata>'], $value, $cdataCount);
            // Special handling for conditional blocks
            preg_match_all("/<!--\[if(.*?)\]>(.*?)(?:\<\!\-\-)?<!\[endif\]-->/is", $value, $matches);
            if (!empty($matches[0])) {
                $from = [];
                $to   = [];
                foreach ($matches[0] as $key=>$match) {
                    $from[]   = $match;
                    $startTag = '<mcondition>';
                    $endTag   = '</mcondition>';
                    if (str_contains($match, '<!--<![endif]-->')) {
                        $startTag = '<mconditionnonoutlook>';
                        $endTag   = '</mconditionnonoutlook>';
                    }
                    $to[] = $startTag.'<mif>'.$matches[1][$key].'</mif>'.$matches[2][$key].$endTag;
                }
                $value = str_replace($from, $to, $value);
            }

            // Slecial handling for XML tags used in Outlook optimized emails <o:*/> and <w:/>
            $value = preg_replace_callback(
                "/<\/*[o|w|v]:[^>]*>/is",
                fn($matches) => '<mencoded>'.htmlspecialchars($matches[0]).'</mencoded>',
                $value, -1, $needsDecoding);

            // Slecial handling for script tags
            $value = preg_replace_callback(
                "/<script>(.*?)<\/script>/is",
                fn($matches) => '<mscript>'.base64_encode($matches[0]).'</mscript>',
                $value, -1, $needsScriptDecoding);

            // Special handling for HTML comments
            $value = str_replace(['<!-->', '<!--', '-->'], ['<mcomment></mcomment>', '<mcomment>', '</mcomment>'], $value, $commentCount);

            // detect if there is any unicode character in the passed string
            $hasUnicode = strlen($value) != strlen(utf8_decode($value));

            // Encode the incoming value before cleaning, it convert unicode to encoded strings
            $value = $hasUnicode ? rawurlencode($value) : $value;

            $value = self::getFilter(true)->clean($value, 'html');

            // After cleaning encode the value
            $value = $hasUnicode ? rawurldecode($value) : $value;

            // Was a doctype found?
            if ($doctypeFound) {
                $value = "$doctype[0]$value";
            }

            if ($cdataCount) {
                $value = str_replace(['<mcdata>', '</mcdata>'], ['<![CDATA[', ']]>'], $value);
            }

            if (!empty($matches[0])) {
                // Special handling for conditional blocks
                $value = preg_replace("/<mconditionnonoutlook><mif>(.*?)<\/mif>(.*?)<\/mconditionnonoutlook>/is", '<!--[if$1]>$2<!--<![endif]-->', $value);
                $value = preg_replace("/<mcondition><mif>(.*?)<\/mif>(.*?)<\/mcondition>/is", '<!--[if$1]>$2<![endif]-->', $value);
            }

            if ($commentCount) {
                $value = str_replace(['<mcomment>', '</mcomment>'], ['<!--', '-->'], $value);
            }

            if ($needsDecoding) {
                $value = preg_replace_callback(
                    "/<mencoded>(.*?)<\/mencoded>/is",
                    fn($matches) => htmlspecialchars_decode($matches[1]),
                    $value);
            }

            if ($needsScriptDecoding) {
                $value = preg_replace_callback(
                    "/<mscript>(.*?)<\/mscript>/is",
                    fn($matches) => base64_decode($matches[1]),
                    $value);
            }
        }

        return $value;
    }

    /**
     * Allows tags 'b', 'i', 'u', 'em', 'strong', 'a', 'span'.
     *
     * @param $data
     *
     * @return mixed|string
     */
    public static function strict_html($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = self::strict_html($val);
            }
        }

        return self::getFilter(true, true)->clean($value, 'html');
    }

    /**
     * Converts UTF8 into Latin.
     *
     * @param $value
     *
     * @return mixed
     */
    public static function transliterate($value)
    {
        $transId = 'Any-Latin; Latin-ASCII';
        if (function_exists('transliterator_transliterate') && $trans = \Transliterator::create($transId)) {
            // Use intl by default
            return $trans->transliterate($value);
        }

        return \URLify::transliterate((string) $value);
    }
}
