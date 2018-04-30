<?php

namespace testframework;

/**
 * @author albert
 */
class Request extends Object
{

    /**
     * @return string
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        if ($this->getMethod() === 'POST') {
            return $_POST;
        } else {
            parse_str(file_get_contents('php://input'), $params);
            return $params;
        }
    }

}