<?php

namespace testapplication\models;

use testframework\Helper;
use testframework\Model;
use testframework\validators\CallbackValidator;
use testframework\validators\StringValidator;
use testframework\validators\Validator;

/**
 * @author albert
 */
class RegisterForm extends Model
{

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

    public function inputFields()
    {
        return ['login', 'password'];
    }

    public function outputFields()
    {
        return ['id'];
    }

    public function validators()
    {
        return [
            [
                'class' => StringValidator::class,
                'field' => 'login',
                'minLength' => 3,
                'maxLength' => 16,
            ],
            [
                'class' => StringValidator::class,
                'field' => 'password',
                'minLength' => 6,
                'maxLength' => 32,
            ],
            [
                'class' => CallbackValidator::class,
                'field' => 'login',
                'callback' => [$this, 'validateLoginUnique'],
            ],
        ];
    }

    public function validateLoginUnique(Validator $validator)
    {
        if (strlen($this->login)) {
            $sameAccount = Account::find('login = :login', [
                'login' => $this->login,
            ]);
            if ($sameAccount !== null) {
                $validator->errors[] = "User '{$this->login}' already exists.";
            }
        }
    }

    /**
     * @return Account
     */
    public function register()
    {
        $account = new Account();
        $account->login = $this->login;
        $account->password = $this->password;
        $account->access_key = Helper::generateRandomString(32);

        $account->save();

        $this->id = $account->id;
    }

}