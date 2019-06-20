<?php

namespace models;

class Comment
{
    //propeties
    private $id;
    private $image_id;
    private $owner_id;
    private $content;
    private $creation_date;

    //getters
    public function get_id() { return $this->id; }
    public function get_image_id() { return $this->image_id; }
    public function get_owner_id() { return $this->owner_id; }
    public function get_content() { return $this->content; }
    public function get_creation_date() {return $this->creation_date; }
    // ...

    //setters
    public function set_id($value) { $this->id = $value; }
    public function set_image_id($value) { $this->image_id = $value; }
    public function set_owner_id($value) { $this->owner_id = $value; }
    public function set_content($value) { $this->content = $value; }
    public function set_creation_date($value) {$this->creation_date = $value; }

    //methods
    public function properties(){ return get_object_vars($this); }
    public function properties_names(){ return array_keys(get_object_vars($this)); }
    public function to_tring() { return "id : $this->id, image_id : $this->image_id, owner_id : $this->owner_id, content : $this->content, creation_date : $this->creation_date"; }
}