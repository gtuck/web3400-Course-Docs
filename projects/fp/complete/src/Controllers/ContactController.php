<?php
// filepath: projects/05/src/Controllers/ContactController.php
namespace App\Controllers;

use App\Controller;
use App\Models\Contact;
use App\Support\Validator;

class ContactController extends Controller
{
    public function show()
    {
        $this->render('contact', [
            'title' => 'Contact Us',
            'old'   => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    public function submit()
    {
        // CSRF Protection: Validate token first
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed. Please try again.', 'is-danger');
            $this->redirect('/contact');
        }

        // Sanitize input
        $data = [
            'name'    => trim($_POST['name'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
        ];

        // Validate using Validator class
        $validationErrors = Validator::validate($data, [
            'name'    => 'required|max:255',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        // Flatten errors to simple array
        $errors = Validator::flattenErrors($validationErrors);

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->flash($err, 'is-warning');
            }
            return $this->render('contact', [
                'title' => 'Contact Us',
                'old'   => $data,
            ]);
        }

        // Persist via BaseModel-powered Contact model
        Contact::create($data);

        // Flash success and redirect (POST/Redirect/GET)
        $this->flash('Thanks! Your message has been received.', 'is-success');
        $this->redirect('/contact');
    }
}
