<?php

namespace testframework;

use Exception;

/**
 * @author albert
 */
class Router extends Object
{

    const REGEX_PATTERN = '/^(?P<method>(?:POST)|(?:GET)|(?:PATCH)|(?:DELETE)|(?:PUT))\s+(?P<path>(?:\/\:?[a-z][a-zA-Z0-9-_]*)+)$/';

    public $rules = [];

    public function init()
    {
        parent::init();
    }

    public function getRoute()
    {
        foreach ($this->rules as $pattern => $route) {
            $result = preg_match(static::REGEX_PATTERN, $pattern, $m);
            if ($result !== 1) {
                throw new Exception("Rule pattern '$pattern' has wrong format.");
            }

            if ($m['method'] !== $_SERVER['REQUEST_METHOD']) {
                continue;
            }

            $patternParts = explode('/', substr($m['path'], 1));

            $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $requestParts = explode('/', substr($requestPath, 1));

            if (count($patternParts) !== count($requestParts)) {
                continue;
            }

            $params = [];
            $isMatch = true;
            foreach ($patternParts as $i => $part) {
                if (strpos($part, ':') === 0) {
                    $params[substr($part, 1)] = $requestParts[$i];
                } elseif ($part !== $requestParts[$i]) {
                    $isMatch = false;
                    break;
                }
            }

            if ($isMatch) {
                return [$route, $params];
            }
        }

        return false;
    }

}