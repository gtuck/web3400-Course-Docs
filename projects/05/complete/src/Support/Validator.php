<?php
/**
 * Validator Class
 *
 * PURPOSE:
 * Provides reusable validation functionality for form data and user input.
 * Centralizes validation logic to promote DRY principles and consistency.
 *
 * USAGE EXAMPLE:
 * ```php
 * $errors = Validator::validate($_POST, [
 *     'name' => 'required|max:255',
 *     'email' => 'required|email',
 *     'age' => 'required|numeric|min:18'
 * ]);
 *
 * if (!empty($errors)) {
 *     // Handle validation errors
 *     foreach ($errors as $field => $messages) {
 *         foreach ($messages as $message) {
 *             echo "{$field}: {$message}\n";
 *         }
 *     }
 * }
 * ```
 *
 * SUPPORTED RULES:
 * - required: Field must be present and not empty
 * - email: Must be a valid email address
 * - numeric: Must be a number
 * - min:X: Minimum value (for numbers) or length (for strings)
 * - max:X: Maximum value (for numbers) or length (for strings)
 * - in:a,b,c: Value must be one of the specified options
 */

namespace App\Support;

class Validator
{
    /**
     * Validate data against a set of rules
     *
     * @param array $data The data to validate (e.g., $_POST)
     * @param array $rules Validation rules for each field
     *                     Format: ['fieldName' => 'rule1|rule2:param|rule3']
     * @return array Associative array of errors (empty if validation passes)
     *               Format: ['fieldName' => ['error message 1', 'error message 2']]
     *
     * EXAMPLE:
     * ```php
     * $errors = Validator::validate($_POST, [
     *     'username' => 'required|max:50',
     *     'password' => 'required|min:8',
     *     'role' => 'required|in:admin,user,guest'
     * ]);
     * ```
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            // Split rules by pipe: 'required|max:255' => ['required', 'max:255']
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                // Parse rule and parameter: 'max:255' => ['max', '255']
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                // Apply validation rule
                $error = self::applyRule($field, $value, $ruleName, $ruleParam);

                if ($error) {
                    $errors[$field] = $errors[$field] ?? [];
                    $errors[$field][] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * Apply a single validation rule to a field
     *
     * @param string $field Field name (for error messages)
     * @param mixed $value Field value
     * @param string $rule Rule name (e.g., 'required', 'email')
     * @param string|null $param Optional rule parameter (e.g., '255' for 'max:255')
     * @return string|null Error message if validation fails, null if passes
     */
    private static function applyRule(string $field, mixed $value, string $rule, ?string $param): ?string
    {
        return match ($rule) {
            'required' => self::validateRequired($field, $value),
            'email' => self::validateEmail($field, $value),
            'numeric' => self::validateNumeric($field, $value),
            'min' => self::validateMin($field, $value, $param),
            'max' => self::validateMax($field, $value, $param),
            'in' => self::validateIn($field, $value, $param),
            default => null, // Unknown rules are ignored
        };
    }

    /**
     * Validate that a field is present and not empty
     */
    private static function validateRequired(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return ucfirst($field) . " is required.";
        }
        return null;
    }

    /**
     * Validate that a field is a valid email address
     */
    private static function validateEmail(string $field, mixed $value): ?string
    {
        // Skip validation if value is empty (use 'required' rule separately)
        if ($value === null || $value === '') {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ucfirst($field) . " must be a valid email address.";
        }
        return null;
    }

    /**
     * Validate that a field is numeric
     */
    private static function validateNumeric(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return ucfirst($field) . " must be a number.";
        }
        return null;
    }

    /**
     * Validate minimum value (for numbers) or length (for strings)
     *
     * @param string $param The minimum value/length
     */
    private static function validateMin(string $field, mixed $value, ?string $param): ?string
    {
        if ($value === null || $value === '' || $param === null) {
            return null;
        }

        $min = (int) $param;

        // For numbers, check value
        if (is_numeric($value)) {
            if ((float) $value < $min) {
                return ucfirst($field) . " must be at least {$min}.";
            }
        }
        // For strings, check length
        else if (is_string($value)) {
            if (mb_strlen($value) < $min) {
                return ucfirst($field) . " must be at least {$min} characters.";
            }
        }

        return null;
    }

    /**
     * Validate maximum value (for numbers) or length (for strings)
     *
     * @param string $param The maximum value/length
     */
    private static function validateMax(string $field, mixed $value, ?string $param): ?string
    {
        if ($value === null || $value === '' || $param === null) {
            return null;
        }

        $max = (int) $param;

        // For numbers, check value
        if (is_numeric($value)) {
            if ((float) $value > $max) {
                return ucfirst($field) . " must not exceed {$max}.";
            }
        }
        // For strings, check length
        else if (is_string($value)) {
            if (mb_strlen($value) > $max) {
                return ucfirst($field) . " must not exceed {$max} characters.";
            }
        }

        return null;
    }

    /**
     * Validate that a field value is in a list of allowed values
     *
     * @param string $param Comma-separated list of allowed values (e.g., 'admin,user,guest')
     */
    private static function validateIn(string $field, mixed $value, ?string $param): ?string
    {
        if ($value === null || $value === '' || $param === null) {
            return null;
        }

        $allowed = explode(',', $param);

        if (!in_array($value, $allowed, true)) {
            $list = implode(', ', $allowed);
            return ucfirst($field) . " must be one of: {$list}.";
        }

        return null;
    }

    /**
     * Get all error messages as a flat array
     *
     * Useful for displaying all errors in a simple list.
     *
     * @param array $errors Errors array from validate()
     * @return array Flat array of all error messages
     *
     * EXAMPLE:
     * ```php
     * $errors = Validator::validate($data, $rules);
     * $messages = Validator::flattenErrors($errors);
     * foreach ($messages as $message) {
     *     echo "- {$message}\n";
     * }
     * ```
     */
    public static function flattenErrors(array $errors): array
    {
        $flat = [];
        foreach ($errors as $fieldErrors) {
            foreach ($fieldErrors as $message) {
                $flat[] = $message;
            }
        }
        return $flat;
    }
}
