<?php

namespace testframework\validators;

/**
 * @author albert
 */
class StringValidator extends Validator {

    /**
     * @var int
     */
    public $minLength;

    /**
     * @var int
     */
    public $maxLength;

    /**
     * @var string
     */
    public $minLengthMessage = 'Wrong length.';

    /**
     * @var string
     */
    public $maxLengthMessage = 'Wrong length.';

    public function validate() {
        $value = $this->model->{$this->field};
        if (!is_string($value)) {
            $this->errors[] = 'Value is not string.';
        } elseif ($this->maxLength !== null && mb_strlen($value, 'UTF-8') > $this->maxLength) {
            $this->errors[] = $this->maxLengthMessage;
        } elseif ($this->minLength !== null && mb_strlen($value, 'UTF-8') < $this->minLength) {
            $this->errors[] = $this->minLengthMessage;
        }
    }

}
