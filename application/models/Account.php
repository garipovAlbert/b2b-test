<?php

namespace testapplication\models;

use testframework\DbModel;
use testframework\Helper;

/**
 * @author albert
 */
class Account extends DbModel {

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $access_key;

    /**
     * @inheritdoc
     */
    public static function table() {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function dbFields() {
        return ['id', 'login', 'password', 'access_key'];
    }

    /**
     * @inheritdoc
     */
    public function outputFields() {
        return ['id', 'login'];
    }

}
