<?php

namespace testframework\validators;

use testframework\Object;

/**
 * @author albert
 */
abstract class Validator extends Object {

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $field;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param mixed $value
     */
    abstract public function validate();
}
