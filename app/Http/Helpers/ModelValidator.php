<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Model;

/*****************************************
 *the point of model validator is to ensure that when required to return either the instance of a class needed
 *Or false, checking if a post request has produced errors, therefore the form will need to be refilled
 *Or a get request for a edit of existing class therefore find instance and return it
 *else return false for a new instance
 *****************************************/

class ModelValidator
{
    private $classType;
    private $class_id;
    private $oldInput;
    private $idField;

    public function __construct($classType, $class_id, $oldInput, $idField = "id")
    {
        $this->classType = $classType;
        $this->class_id = $class_id;
        $this->oldInput = $oldInput;
        $this->idField = $idField;
    }

    //check if a model, then validate input
    public function validate()
    {
        $baseClass = false;

        if ($this->isModel()) {
            $baseClass = $this->validateStep();
        }
        //otherwise class instance is not found or a form has not been submitted therefore return false

        return $baseClass;
    }

    //ensure either an std object of the failed post submission or the requested class instance is returned
    private function validateStep()
    {
        //if a previous form submission has been completed convert to stdclass and return
        if (!empty($this->oldInput)) {
            return $baseClass = (object) $this->oldInput;
        }

        if (isset($this->class_id)) {
            //if a edit is requested find instance and return
            //using first to return only the first element with that id, alternativly use [0]
            //dd($this->classType::where($this->idField, $this->class_id)->get());
            $baseClass = $this->classType::where($this->idField, $this->class_id)->get()->first();
            
            //null check if returned id value does not exist.
            if ($baseClass == null) {
                $baseClass = false;
            }

            return $baseClass;
        }
    }

    //test function to ensure only models are passed int
    public function isModel()
    {
        $class = new $this->classType();

        //full path to eloquent model needs to be passed
        if (is_a($class, 'Illuminate\Database\Eloquent\Model')) {
            return true;
        } else {
            return false;
        }
    }
}
