<?php

namespace testframework\auth;

use testframework\Helper;
use testframework\Object;

/**
 * @author albert
 */
class User extends Object {

    private $_account;

    /**
     * @var string
     */
    public $accountClass;

    /**
     * @var string
     */
    public $keyField;

    /**
     * @return boolean
     */
    public function auth() {
        $headers = Helper::getHeaders();
        if (!isset($headers['Authorization'])) {
            return false;
        }

        $pm = preg_match('/^Bearer\s+(?P<accessKey>[^\s]+)/', $headers['Authorization'], $m);
        if ($pm !== 1) {
            return false;
        }

        $this->_account = $this->accountClass::find($this->keyField . ' = :access_key', [
                    'access_key' => $m['accessKey'],
        ]);

        return $this->_account !== null;
    }

    public function getAccount() {
        return $this->_account;
    }

    public function getId() {
        return $this->getAccount()->id;
    }

}
