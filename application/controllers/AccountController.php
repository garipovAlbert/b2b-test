<?php

namespace testapplication\controllers;

use testapplication\models\LoginForm;
use testapplication\models\RegisterForm;
use testframework\App;
use testframework\Controller;
use testframework\DataObject;

/**
 * @author albert
 */
class AccountController extends Controller
{

    /**
     * @return DataObject
     */
    public function actionRegister()
    {
        $form = new RegisterForm();
        $form->setData(App::get()->getRequest()->getParams());

        $validation = $form->validate();
        if ($validation->hasErrors()) {
            return [422, $validation];
        }

        $form->register();

        return [200, $form];
    }

    /**
     * @return DataObject
     */
    public function actionLogin()
    {
        $form = new LoginForm();
        $form->setData(App::get()->getRequest()->getParams());

        $validation = $form->validate();
        if ($validation->hasErrors()) {
            return [422, $validation];
        }

        return [200, $form];
    }

}