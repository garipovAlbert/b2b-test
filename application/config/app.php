<?php

return [
    'class' => testframework\App::class,
    'controllerNamespace' => 'testapplication\controllers',
    'rootDir' => realpath(__DIR__ . '/../'),
    'router' => [
        'rules' => [
            'POST /register' => 'account/register',
            'POST /login' => 'account/login',
            'POST /new-recipe' => 'recipe/create',
            'GET /recipe/:id' => 'recipe/read',
            'PATCH /recipe/:id' => 'recipe/update',
            'DELETE /recipe/:id' => 'recipe/delete',
        ],
    ],
    'db' => [
        'dsn' => 'pgsql:dbname=b2b-test;host=localhost',
        'username' => 'postgres',
        'password' => 'postgres',
    ],
    'user' => [
        'accountClass' => \testapplication\models\Account::class,
        'keyField' => 'access_key',
    ],
];
