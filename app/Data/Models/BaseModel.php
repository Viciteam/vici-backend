<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class BaseModel
 *
 * @package App\Data\Models
 */
class BaseModel extends Model
{
    protected $debugMode = true;
    protected $errors;
    protected $messages = [
        'email' => 'The :attribute must be a valid email address.',
        'numeric' => 'The :attribute must be numeric.',
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute must be string.',
        'unique' => 'The :attribute is already taken.',
        'url' => 'The :attribute must be a valid URL.',
    ];
    protected $rules = [];

    //region Getters

    /**
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->debugMode;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
    //endregion Getters

    //region Setters
    /**
     * @param bool $debug
     * @return self
     */
    public function setDebugMode($debug = false)
    {
        $this->debugMode = $debug;
        return $this;
    }
    //endregion Setters

    /**
     * Initializes a new class and returns an instantiated model of the model
     *
     * @param array $data
     * @return Object
     */
    public function init(array $data = [])
    {
        $class = $this->getClass();
        $new_model = new $class($data);
        
        return $new_model;
    }

    /**
     * Get model class
     *
     * @return string
     */
    public function getClass()
    {
        return static::class;
    }

    /**
     * Override the model save method
     *
     * @param array $data
     * @return boolean
     */
    public function save(array $data = []) : bool
    {
        if (!$this->validate($data)) {
            return false;
        }

        try {
            parent::fill($data);
            parent::save();
            return true;
        } catch (\Exception $e) {
            $this->errors = $this->getDebugMode() ? $e->getMessage() : "An error has occurred while saving the resource.";
            return false;
        }
    }

    /**
     * Validate base on the model's rules
     *
     * @param array $data
     * @return boolean
     */
    public function validate(array $data = []) : bool
    {
        $validator = Validator::make($data, $this->rules, $this->messages);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
            return false;
        }

        return true;
    }
}
