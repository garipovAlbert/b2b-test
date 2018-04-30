<?php

namespace testframework\validators;

/**
 * @author albert
 */
class CallbackValidator extends Validator {

    /**
     * @var callback
     */
    public $callback;

    //put your code here
    public function validate() {
        call_user_func($this->callback, $this);
    }

}
