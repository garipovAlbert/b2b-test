<?php

namespace testframework;

/**
 * @author albert
 */
abstract class DbModel extends Model
{

    /**
     * @var boolean
     */
    public $isNew = true;

    /**
     * @var string
     */
    public $id;

    /**
     * @return string
     */
    abstract public static function table();

    /**
     * @return array
     */
    abstract public function dbFields();

    /**
     * @param string $condition
     * @param array $params
     * @return static
     */
    public static function find($condition, $params = [])
    {

        $data = App::get()->db->find(static::table(), $condition, $params);

        if (!$data) {
            return null;
        }

        $model = new static;

        foreach ($data as $name => $value) {
            $model->$name = $value;
        }

        $model->isNew = false;
        return $model;
    }

    /**
     * @param string $condition
     * @param array $params
     * @return static[]
     */
    public static function findAll($condition, $params = [])
    {

        $data = App::get()->db->findAll(static::table(), $condition, $params);

        $models = [];
        foreach ($data as $row) {
            $model = new static;
            foreach ($row as $name => $value) {
                $model->$name = $value;
            }
            $model->isNew = false;
            $models[] = $model;
        }

        return $models;
    }

    public function save()
    {
        $saveData = [];
        foreach ($this->dbFields() as $field) {
            $saveData[$field] = $this->$field;
        }

        $db = App::get()->db;
        if ($this->isNew) {
            unset($saveData['id']);
            $db->insert(static::table(), $saveData);
            $this->id = $db->getLastInsertId();
            $this->isNew = false;
        } else {
            $db->update(static::table(), 'id = :id', $saveData);
        }
    }

    public function delete()
    {
        App::get()->db->delete(static::table(), 'id = :id', [
            'id' => $this->id,
        ]);
    }

}