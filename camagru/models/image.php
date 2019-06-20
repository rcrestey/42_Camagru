<?php

namespace models;

class Image
{
    //propeties
    private $id;
    private $user_id;
    private $path;
    private $likes;
    private $comments;
    private $creation_date;

    //getters
    public function get_id() { return $this->id; }
    public function get_user_id() { return $this->user_id; }
    public function get_path() { return $this->path; }
    public function get_likes() { return $this->likes; }
    public function get_comments() { return $this->comments; }
    public function get_creation_date() { return $this->creation_date; }

    // ...

    //setters
    public function set_id($value) { $this->id = $value; }
    public function set_user_id($value) { $this->user_id = $value; }
    public function set_path($value) { $this->path = $value; }
    public function set_likes($value) { $this->likes = $value; }
    public function set_comments($value) { $this->comments = $value; }
    public function set_creation_date($value) { $this->creation_date = $value; }

    //methods
    public function properties(){ return get_object_vars($this); }
    public function properties_names(){ return array_keys(get_object_vars($this)); }
    public function to_tring() { return "id : $this->id, user_id : $this->user_id, path : $this->path, likes : $this->likes, comments : $this->comments, creation_date : $this->creation_date"; }
}