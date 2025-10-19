<?php
/**
 * ContactController
 *
 * PURPOSE:
 * Handles contact form display and submission
 *
 * RESPONSIBILITIES:
 * - Display contact form with empty or repopulated fields
 * - Validate contact form submissions
 * - Store valid submissions in the database
 * - Provide user feedback via flash messages
 * - Implement CSRF protection for form security
 *
 * ROUTES:
 * - GET /contact  -> show()
 * - POST /contact -> submit()
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
     * Shows an empty contact form ready for user input.
     * The form includes CSRF protection token automatically.
     *
     * ROUTE: GET /contact
     *
     * @return void
     */
    public function show()
    {
        $this->render('contact', [
            'title' => 'Contact Us',
            'old'   => ['name' => '', 'email' => '', 'message' => ''],
        ]);
    }

    /**
     * Process contact form submission
     *
     * PROCESS:
     * 1. Validate CSRF token for security
     * 2. Extract and sanitize form input
     * 3. Validate all fields
     * 4. On success: Save to database, flash success, redirect (PRG pattern)
     * 5. On failure: Flash errors, re-render form with old input
     *
     * VALIDATION RULES:
     * - name: Required, max 255 characters
     * - email: Required, valid email format
     * - message: Required
     *
     * ROUTE: POST /contact
     *
     * SECURITY:
     * - CSRF token validation
     * - Input sanitization with trim()
     * - Email validation with FILTER_VALIDATE_EMAIL
     * - Length validation to prevent overflow
     *
     * @return void
     */
    public function submit()
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
