<?php

namespace models;

class User
{
    //propeties
    private $id;
    private $mail;
    private $username;
    private $password;
    private $notification;
    private $keycheck;
    private $confirmed;
    private $last_seen;

    //getters
    public function get_id() { return $this->id; }
    public function get_mail() { return $this->mail; }
    public function get_username() { return $this->username; }
    public function get_password() { return $this->password; }
    public function get_notification() { return $this->notification; }
    public function get_keycheck() { return $this->keycheck; }
    public function get_confirmed() { return $this->confirmed; }
    // ...

    //setters
    public function set_id($value) { $this->id = $value; }
    public function set_mail($value) { $this->mail = $value; }
    public function set_username($value) { $this->username = $value; }
    public function set_password($value) { $this->password = $value; }
    public function set_notification($value) { $this->notification = $value; }
    public function set_keycheck($value) { $this->keycheck = $value; }
    public function set_confirmed($value) { $this->confirmed = $value; }
    public function set_last_seen($value) { $this->last_seen = $value; }

    //methods
    public function properties(){ return get_object_vars($this); }
    public function properties_names(){ return array_keys(get_object_vars($this)); }
    public function to_string() { return "id : $this->id, mail : $this->mail, username : $this->username, password : $this->password, notification : $this->notification, keycheck : $this->keycheck, confirmed : $this->confirmed, lastSeen: $this->last_seen"; }
}