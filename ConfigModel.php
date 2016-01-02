<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 30/12/15
 * Time: 05:00 م
 */

namespace ConfigModel;


use Illuminate\Database\Eloquent\Model;

abstract class ConfigModel extends Model
{
    protected $key_column;
    protected $value_column;


    //The key and value column are, by default, set to 'key' and 'value' respectively
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->key_column   = 'key';
        $this->value_column = 'value';

    }

    //But you can set them otherwise
    public function setKeyValue($key, $value) {
        $this->key_column = $key;
        $this->value_column = $value;
    }

    //Give your configs as an array and they will return as an instance of the Collection class or just a normal array
    //You can also specify if you want it to fail or not as the second parameter
    //It will return a normal array by default, or a collection if you set the third parameter to true

    public function  getConfigs(array $configs, $fail = false , $collection = false) {
        $grab_method = $fail ? 'firstOrFail' : 'first';
        $config_values = [];
        foreach ($configs as $config) {
            $grabbed_config = $this->where($this->key_column, $config)->$grab_method();

            $config_values[$config] = count($grabbed_config) ? $grabbed_config[$this->value_column] : '';

        }
        return $collection ? collect($config_values) : $config_values;

    }

    //Set configs. Pass your configs as an associative array of key => value
    public function setConfigs(array $configs) {
        foreach($configs as $config_key => $config_value) {
            $this[$this->key_column] = $config_key;
            $this[$this->value_column] = $config_value;
            $this->save();
        }
    }

    //Update configs. Pass your configs as an asssociative array of key => new value
    public function updateConfigs(array $configs) {
        foreach ($configs as $config_key => $config_value) {
            $this->where($this->key_column, $config_key)->update([$this->value_column => $config_value]);
        }
    }

    //Delete a config by its key
    public function deleteConfigs(array $configs) {
        foreach ($configs as $config) {
            $this->where($this->key_column, $config)->delete();
        }
    }

}
