<?php

namespace testframework;

/**
 * @author albert
 */
class Response extends Object
{

    /**
     * @param int $statusCode
     * @param DataObject $object
     */
    public function send(int $statusCode, DataObject $object)
    {
        http_response_code($statusCode);
        echo json_encode($object->getData());
    }

}