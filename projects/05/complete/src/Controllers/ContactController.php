<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function show()
    {
        $this->render('contact', [
            'title' => 'Contact Us',
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    public function submit()
    {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if ($name === '' || mb_strlen($name) > 255) $errors[] = "Please provide your name.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email.";
        if ($message === '') $errors[] = "Message is required.";

        if (empty($errors)) {
            Contact::create([
                'name'    => $name,
                'email'   => $email,
                'message' => $message,
            ]);
            // Flash success message and redirect
            $this->flash('Thanks! Your message has been received.', 'is-success');
            $this->redirect('/contact');
        } else {
            foreach ($errors as $err) $this->flash($err, 'is-warning');
            return $this->render('contact', [
                'title'  => 'Contact Us',
                'old'    => compact('name', 'email', 'message'),
            ]);
        }
    }
}
