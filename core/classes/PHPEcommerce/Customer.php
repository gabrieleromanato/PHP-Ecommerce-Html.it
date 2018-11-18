<?php

namespace PHPEcommerce;
use PHPEcommerce\Database as Database;

class Customer {
    private $id;
    private $firstname;
    private $lastname;
    private $email;

    public function __construct($id, $firstname, $lastname, $email) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getFirstName() {
        return $this->firstname;
    }

    public function getLastName() {
        return $this->lastname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function save() {
        if(READONLY) {
            return true;
        }
        $db = new Database();

        $id = $this->id;
        $firstname = $db->escape($this->firstname);
        $lastname = $db->escape($this->lastname);
        $email = $db->escape($this->email);
        $insert = "INSERT INTO customers (id, firstname, lastname, email) VALUES ('$id', '$firstname','$lastname', '$email')";
        return $db->query($insert);

    }

}