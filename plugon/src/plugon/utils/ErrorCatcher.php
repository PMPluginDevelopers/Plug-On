<?php
namespace plugon\utils;

class ErrorCatcher {
    
    /** @var array */
    public $source = [];
    
    protected $errors = [];
    
    public function __construct(array $source) {
        $this->source = $source;
    }
    
    /**
     * Scans for new errors and returns true or false if new is found
     * @return bool
     */
    public function scan(){
        $errors = !empty($this->source["__plugon_error"]) && $this->source["__plugon_error"] ? $this->source["__plugon_error"] : [];
        // check for duplicates
        foreach($errors as $i => $error) {
            if(isset($this->errors[$i]) && $this->errors[$i] === $error) unset($errors[$i]);
        }
        // update
        $this->errors = array_merge($errors, $this->errors);
        if(count($errors) > 0) return true;
        return false;
    }
    
    /**
     * @return string[]
     */
    public function getErrors(){
        return $this->errors;
    }

    /**
     * Add new error without any triggers
     * @param string $error
     */
    public function addError($error) {
        $this->errors[] = $error;
    }

    /**
     * Add an array of errors
     * @param array $errors
     */
     public function addErrors(array $errors) {
         foreach ($errors as $error){
             $this->addError($error);
         }
     }
    /**
     * @return string[]
     */
    public function getSource(){
        return $this->source;
    }

    /**
     * @param array $source
     */
    public function setSource(array $source) {
        $this->source = $source;
    }
     /**
     * @return int
     */
    public function size(){
        return count($this->errors);
    }
    
}