<?php

namespace models;

class Calque
{
    //propeties
    private $id;
    private $path;

    //getters
    public function get_id() { return $this->id; }
    public function get_path() { return $this->path; }
    
    //setters
    public function set_id($value) { $this->id = $value; }
    public function set_path($value) { $this->path = $value; }

    //methods
    public function properties(){ return get_object_vars($this); }
    public function properties_names(){ return array_keys(get_object_vars($this)); }
    public function to_tring() { return "id : $this->id, path : $this->path"; }
}