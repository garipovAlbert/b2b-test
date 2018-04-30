<?php

namespace testframework;

/**
 * @author albert
 */
class Helper
{

    /**
     * @param string $s
     * @return string
     */
    public static function dashesToCamel(string $s): string
    {
        return join(array_map('ucfirst', explode('-', $s)));
    }

    /**
     * Creates an object from configuration.
     * @param array $config
     * @return mixed
     */
    public static function create($config)
    {
        $object = new $config['class'];
        unset($config['class']);

        foreach ($config as $name => $value) {
            $object->$name = $value;
        }

        $object->init();

        return $object;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length = 10): string
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * @return array
     */
    public static function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } elseif (function_exists('http_get_request_headers')) {
            $headers = http_get_request_headers();
        } else {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (strncmp($name, 'HTTP_', 5) === 0) {
                    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$name] = $value;
                }
            }
        }

        return $headers;
    }

    /**
     * @param string $mimeType
     * @return boolean|string
     */
    public static function getImageExt(string $mimeType)
    {
        switch ($mimeType) {
            case ('image/png') : return 'png';
            case ('image/jpg') : return 'jpg';
            case ('image/jpeg') : return 'jpg';
            case ('image/gif') : return 'gif';
            default : return false;
        }
    }

}