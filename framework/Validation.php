<?php

namespace testframework;

/**
 * @author albert
 */
class Validation implements DataObject {

    private $_errors = [];

    /**
     * @param string $field
     * @param string $error
     */
    public function addError($field, $error) {
        $this->_errors[] = [
            'field' => $field,
            'error' => $error,
        ];
    }

    /**
     * @return boolean
     */
    public function hasErrors() {
        return count($this->_errors) > 0;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }

    public function getData(): array {
        return [
            'errors' => $this->getErrors(),
        ];
    }

}
