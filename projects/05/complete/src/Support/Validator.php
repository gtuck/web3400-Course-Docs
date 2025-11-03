<?php
// filepath: projects/05/src/Support/Validator.php
namespace App\Support;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $error = self::applyRule($field, $value, $ruleName, $ruleParam);
                if ($error) {
                    $errors[$field] = $errors[$field] ?? [];
                    $errors[$field][] = $error;
                }
            }
        }
        return $errors;
    }

    private static function applyRule(string $field, mixed $value, string $rule, ?string $param): ?string
    {
        return match ($rule) {
            'required' => ($value === null || $value === '') ? ucfirst($field) . " is required." : null,
            'email' => ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) ? ucfirst($field) . " must be a valid email." : null,
            'max' => ($value && mb_strlen($value) > (int)$param) ? ucfirst($field) . " must not exceed {$param} characters." : null,
            default => null,
        };
    }

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
