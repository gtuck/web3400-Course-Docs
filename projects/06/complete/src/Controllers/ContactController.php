<?php
/**
 * Contact Controller
 *
 * Handles contact form display and submission, including validation
 * and saving messages to the database.
 */

namespace App\Controllers;

use App\Controller;
use App\Models\Contact;
use App\Support\Validator;

class ContactController extends Controller
{
    /**
     * Display the contact form
     *
     * Route: GET /contact
     */
    public function show(): void
    {
        $this->render('contact', [
            'title' => 'Contact Us',
            'old'   => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    /**
     * Process contact form submission
     *
     * Validates the form input and saves to database on success.
     * Uses POST-Redirect-GET pattern to prevent duplicate submissions.
     *
     * Route: POST /contact
     *
     * Validation rules:
     * - Name: required, max 255 characters
     * - Email: required, valid email format
     * - Message: required
     *
     * Security:
     * - CSRF token validation
     * - Input sanitization (trim)
     * - Email format validation
     */
    public function submit(): void
    {
        // CSRF Protection: Validate token before processing
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security token validation failed. Please try again.', 'is-danger');
            $this->redirect('/contact');
        }

        // Sanitize input (trim whitespace)
        $data = [
            'name'    => trim($_POST['name'] ?? ''),
            'email'   => trim($_POST['email'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
        ];

        // Validate input using Validator class
        $validationErrors = Validator::validate($data, [
            'name'    => 'required|max:255',
            'email'   => 'required|email',
            'message' => 'required',
        ]);

        // Flatten errors to simple array of messages
        $errors = Validator::flattenErrors($validationErrors);

        // Process based on validation result
        if (empty($errors)) {
            // Validation passed: Save to database
            Contact::create($data);

            // Flash success message and redirect (POST-Redirect-GET pattern)
            $this->flash('Thanks! Your message has been received.', 'is-success');
            $this->redirect('/contact');
        } else {
            // Validation failed: Flash errors and re-render form
            foreach ($errors as $err) {
                $this->flash($err, 'is-warning');
            }

            // Re-render form with old input so user doesn't have to retype
            return $this->render('contact', [
                'title'  => 'Contact Us',
                'old'    => $data,
            ]);
        }
    }
}
