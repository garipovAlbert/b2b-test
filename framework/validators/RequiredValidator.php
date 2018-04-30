<?php

namespace testframework\validators;

/**
 * @author albert
 */
class RequiredValidator extends Validator {

    public function validate() {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === null || $value === [] || $value === '') {
            $this->errors[] = "{$this->field} in not specified.";
        }
    }

}
