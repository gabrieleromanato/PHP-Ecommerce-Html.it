<?php

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
        $db = new Database();
        $query = $db->select( "SELECT * FROM customers WHERE email = '" . $this->email . "'");
        if(count($query) == 0) {
            $id = $this->id;
            $firstname = $this->firstname;
            $lastname = $this->lastname;
            $email = $this->email;
            $insert = "INSERT INTO customers (id, firstname, lastname, email) VALUES ('$id', '$firstname','$lastname', '$email')";
            return $db->query($insert);
        }
    }

}