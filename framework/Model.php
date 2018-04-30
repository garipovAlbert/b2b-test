<?php

namespace testframework;

/**
 * @author albert
 */
abstract class Model extends Object implements DataObject {

    public function inputFields() {
        return [];
    }

    public function outputFields() {
        return [];
    }

    /**
     * @return array
     */
    public function validators() {
        return [
        ];
    }

    /**
     * @return Validation
     */
    public function validate() {
        $validation = new Validation();

        foreach ($this->validators() as $validatorConfig) {
            $validatorConfig['model'] = $this;
            $validator = Helper::create($validatorConfig);
            $validator->validate();

            foreach ($validator->errors as $error) {
                $validation->addError($validator->field, $error);
            }
        }

        return $validation;
    }

    /**
     * @param array $data
     */
    public function setData($data) {
        foreach ($this->inputFields() as $field) {
            if (array_key_exists($field, $data)) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * @return array
     */
    public function getData() {
        $data = [];
        foreach ($this->outputFields() as $field) {
            $data[$field] = $this->$field;
        }
        return $data;
    }

}
