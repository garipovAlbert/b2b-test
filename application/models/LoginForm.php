<?php

namespace testapplication\models;

use testframework\Model;
use testframework\validators\CallbackValidator;
use testframework\validators\StringValidator;
use testframework\validators\Validator;

/**
 * @author albert
 */
class LoginForm extends Model {

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
    public $id;

    /**
     * @var string
     */
    public $accessKey;

    public function inputFields() {
        return ['login', 'password'];
    }

    public function outputFields() {
        return ['id', 'accessKey'];
    }

    public function validators() {
        return [
            [
                'class' => StringValidator::class,
                'field' => 'login',
            ],
            [
                'class' => StringValidator::class,
                'field' => 'password',
            ],
            [
                'class' => CallbackValidator::class,
                'field' => 'password',
                'callback' => [$this, 'authValidation'],
            ],
        ];
    }

    /**
     * @return string|boolean
     */
    public function authValidation(Validator $validator) {
        $account = Account::find('login = :login AND password = :password', [
                    'login' => $this->login,
                    'password' => $this->password,
        ]);

        if ($account === null) {
            $validator->errors[] = 'Incorrect login or password.';
        }

        $this->id = $account->id;
        $this->accessKey = $account->access_key;
    }

}
