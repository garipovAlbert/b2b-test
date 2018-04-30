<?php

namespace testframework;

use Exception;

/**
 * @author albert
 */
abstract class Object {


    public function init() {
        
    }

    /**
     * @param mixed $var
     * @return mixed
     * @throws Exception
     */
    public function __get($var) {
        $getter = 'get' . $var;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } else {
            throw new Exception("Inexistent property: $var");
        }
    }

    /**
     * @param mixed $var
     * @param mixed $value
     * @throws Exception
     */
    public function __set($var, $value) {
        $setter = 'set' . $var;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            if (method_exists($this, 'get' . $var)) {
                throw new Exception("property $var is read-only");
            } else {
                throw new Exception("Inexistent property: $var");
            }
        }
    }

}
