<?php

namespace App\Controllers;

use App\Controller; // base from P03 with render()
use App\Models\Contact;

class ContactController extends Controller
{
    public function show()
    {
        $this->render('contact', [
            'errors' => [],
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
            'status' => null,
        ]);
    }

    public function submit()
    {
        $name    = trim($_POST['name']    ?? '');
        $email   = trim($_POST['email']   ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }
        if ($message === '') {
            $errors[] = 'Message is required.';
        }

        if ($errors) {
            return $this->render('contact', [
                'errors' => $errors,
                'old'    => compact('name', 'email', 'message'),
                'status' => null,
            ]);
        }

        // Persist via BaseModel-powered Contact model
        Contact::create([
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
        ]);

        $this->render('contact', [
            'errors' => [],
            'old'    => ['name' => '', 'email' => '', 'message' => ''],
            'status' => 'Thanks! Your message has been received.',
        ]);
    }
}
