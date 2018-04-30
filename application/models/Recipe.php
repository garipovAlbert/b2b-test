<?php

namespace testapplication\models;

use testframework\App;
use testframework\DbModel;
use testframework\Helper;
use testframework\validators\CallbackValidator;
use testframework\validators\StringValidator;
use testframework\validators\Validator;

/**
 * @author albert
 */
class Recipe extends DbModel
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $account_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $image_url;

    /**
     * @var string
     */
    public $imageFile;

    public static function table(): string
    {
        return 'recipe';
    }

    public function dbFields(): array
    {
        return ['id', 'account_id', 'name', 'description', 'image_url'];
    }

    public function inputFields(): array
    {
        return ['name', 'description'];
    }

    public function outputFields(): array
    {
        return ['id', 'name', 'account_id', 'description', 'image_url'];
    }

    public function validators()
    {
        return [
            [
                'class' => StringValidator::class,
                'field' => 'name',
                'minLength' => 1,
                'minLengthMessage' => 'Name must be specified',
                'maxLength' => 32,
            ],
            [
                'class' => StringValidator::class,
                'field' => 'description',
                'maxLength' => 255,
            ],
            [
                'class' => CallbackValidator::class,
                'field' => 'imageFile',
                'callback' => [$this, 'imageFileValidation'],
            ],
        ];
    }

    public function imageFileValidation(Validator $validator)
    {
        if ($this->imageFile) {
            if (!in_array($this->imageFile['type'], ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'])) {
                $validator->errors[] = 'Wrong format.';
                return;
            }
        }
    }

    public function save()
    {
        if ($this->imageFile) {
            $path = '/images/' . Helper::generateRandomString(8) . '.' . Helper::getImageExt($this->imageFile['type']);
            $filepath = App::get()->rootDir . '/web' . $path;
            move_uploaded_file($this->imageFile['tmp_name'], $filepath);
            $this->image_url = $path;
        }

        parent::save();
    }

}