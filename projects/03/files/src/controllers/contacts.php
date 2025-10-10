<?php

class Contacts
{
    public function index()
    {
        require "src/models/contact.php";

        $model = new Contact;

        $contacts = $model->getData();

        require "views/contacts_index.php";
    }
}
