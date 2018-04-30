<?php

namespace testapplication\controllers;

use testapplication\models\Recipe;
use testframework\App;
use testframework\Controller;
use testframework\DataObject;
use testframework\HttpException;

/**
 * @author albert
 */
class RecipeController extends Controller
{

    public function init()
    {
        parent::init();

        if (in_array($this->actionId, ['create', 'read', 'update', 'delete'])) {
            $authorized = App::get()->getUser()->auth();
            if (!$authorized) {
                throw new HttpException(401, 'You are requesting with an invalid credential.');
            }
        }
    }

    /**
     * @return DataObject
     */
    public function actionCreate()
    {
        $recipe = new Recipe();

        $recipe->setData(App::get()->getRequest()->getParams());

        if (isset($_FILES['imageFile'])) {
            $recipe->imageFile = $_FILES['imageFile'];
        }

        $recipe->account_id = App::get()->getUser()->getId();

        $validation = $recipe->validate();
        if ($validation->hasErrors()) {
            return [422, $validation];
        }

        $recipe->save();

        return [201, $recipe];
    }

    /**
     * @return array
     */
    public function actionRead($id)
    {
        $recipe = Recipe::find('id = :id', [
            'id' => $id,
        ]);

        if ($recipe === null) {
            throw new HttpException(404, 'Recipe not found.');
        }

        return [200, $recipe];
    }

    /**
     * @return array
     */
    public function actionUpdate($id)
    {
        $recipe = Recipe::find('id = :id', [
            'id' => $id,
        ]);

        if ($recipe === null) {
            throw new HttpException(404, 'Recipe not found.');
        }

        $recipe->setData(App::get()->getRequest()->getParams());

        $validation = $recipe->validate();
        if ($validation->hasErrors()) {
            return [422, $validation];
        }

        $recipe->save();

        return [200, $recipe];
    }

    /**
     * @return array
     */
    public function actionDelete($id)
    {
        $recipe = Recipe::find('id = :id', [
            'id' => $id,
        ]);

        if ($recipe === null) {
            throw new HttpException(404, 'Recipe not found.');
        }

        $recipe->delete();

        return [200, $recipe];
    }

}