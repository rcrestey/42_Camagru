<?php

namespace models;

class Like
{
    //propeties
    private $id;
    private $liker_id;
    private $image_id;
    private $creation_date;

    //getters
    public function get_id() { return $this->id; }
    public function get_liker_id() { return $this->liker_id; }
    public function get_image_id() { return $this->image_id; }
    public function get_creation_date() { return $this->creation_date; }
    // ...

    //setters
    public function set_id($value) { $this->id = $value; }
    public function set_liker_id($value) { $this->liker_id = $value; }
    public function set_image_id($value) { $this->image_id = $value;}
    public function set_creation_date($value) { $this->creation_date = $value; }
   

    //methods
    public function properties(){ return get_object_vars($this); }
    public function properties_names(){ return array_keys(get_object_vars($this)); }
    public function to_tring() { return "id : $this->id, liker_id : $this->liker_id, image_id : $this->image_id, creation_date : $this->creation_date"; }
}