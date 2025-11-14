#!/usr/bin/env php
<?php
/**
 * Model Generator Script
 *
 * PURPOSE:
 * CLI tool to automatically generate model classes from database tables.
 * Introspects table structure and creates a properly formatted model file.
 *
 * USAGE:
 * php scripts/generate-model.php <table_name>
 *
 * EXAMPLES:
 * php scripts/generate-model.php users
 * php scripts/generate-model.php blog_posts
 * php scripts/generate-model.php contact_us
 *
 * FEATURES:
 * - Auto-detects primary key
 * - Excludes timestamp columns (created_at, updated_at, deleted_at)
 * - Converts table names to singular class names (posts → Post)
 * - Generates clean, properly formatted model files with documentation
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load .env so Database::pdo() has credentials
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

use App\Support\Database;
use PDO;

// Parse command-line arguments
[$script, $table] = $argv + [null, null];
if (!$table) {
    fwrite(STDERR, "Usage: php scripts/generate-model.php <table>\n");
    exit(1);
}

// Connect to database
$pdo = Database::pdo();

// Query table structure from INFORMATION_SCHEMA
$sql = "SELECT COLUMN_NAME, COLUMN_KEY
          FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t
      ORDER BY ORDINAL_POSITION";
$stmt = $pdo->prepare($sql);
$stmt->execute([':t' => $table]);
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Validate table exists
if (!$cols) {
    fwrite(STDERR, "Table not found: {$table}\n");
    exit(1);
}

// Extract primary key and fillable columns
$pk = 'id';
$fillable = [];
foreach ($cols as $col) {
    $name = $col['COLUMN_NAME'];

    // Identify primary key
    if ($col['COLUMN_KEY'] === 'PRI') {
        $pk = $name;
        continue;
    }

    // Exclude timestamp columns (auto-managed by database)
    if (in_array($name, ['created_at', 'updated_at', 'deleted_at'], true)) {
        continue;
    }

    $fillable[] = $name;
}

/**
 * Convert snake_case or kebab-case to StudlyCase
 * Example: blog_posts → BlogPosts, user-roles → UserRoles
 */
function studly(string $s): string
{
    $s = str_replace(['-', '_'], ' ', $s);
    $s = ucwords($s);
    return str_replace(' ', '', $s);
}

/**
 * Check if string ends with a suffix
 */
function ends_with(string $s, string $suffix): bool
{
    $len = strlen($suffix);
    if ($len === 0) return true;
    return substr($s, -$len) === $suffix;
}

/**
 * Convert plural table name to singular class name
 * Examples: posts → post, categories → category, analyses → analysis
 */
function singular(string $s): string
{
    if (ends_with($s, 'ies')) return substr($s, 0, -3) . 'y';
    if (ends_with($s, 'ses')) return substr($s, 0, -2);
    if (ends_with($s, 's')) return substr($s, 0, -1);
    return $s;
}

/**
 * Format array as clean PHP array literal syntax
 * Example: ['name', 'email', 'message']
 */
function format_array(array $items): string
{
    if (empty($items)) {
        return '[]';
    }

    $quoted = array_map(fn($item) => "'{$item}'", $items);
    return '[' . implode(', ', $quoted) . ']';
}

// Generate class name from table name
$class = studly(singular($table));

// Generate fillable array with clean syntax
$fillableString = format_array($fillable);

// Model template with documentation
$template = <<<'PHP'
<?php
/**
 * %CLASS% Model
 *
 * PURPOSE:
 * Represents records in the '%TABLE%' database table.
 * Provides CRUD operations via ActiveRecord pattern.
 *
 * DATABASE TABLE: %TABLE%
 * PRIMARY KEY: %PK%
 *
 * USAGE EXAMPLES:
 *
 * // Create a new record
 * $id = %CLASS%::create([
 *     // Add your fields here
 * ]);
 *
 * // Find a specific record
 * $record = %CLASS%::find(1);
 *
 * // Get all records
 * $records = %CLASS%::all(limit: 10, orderBy: 'created_at DESC');
 *
 * // Update a record
 * %CLASS%::update(1, [
 *     // Updated fields
 * ]);
 *
 * // Delete a record
 * %CLASS%::delete(1);
 */

namespace App\Models;

final class %CLASS% extends BaseModel
{
    /** @var string Database table name */
    protected static string $table = '%TABLE%';

    /** @var string Primary key column */
    protected static string $primaryKey = '%PK%';

    /** @var array<string> Columns allowed for mass assignment */
    protected static array $fillable = %FILLABLE%;
}

PHP;

// Replace placeholders with actual values
$code = str_replace(
    ['%CLASS%', '%TABLE%', '%PK%', '%FILLABLE%'],
    [$class, $table, $pk, $fillableString],
    $template
);

// Write model file
$outPath = dirname(__DIR__) . '/src/Models/' . $class . '.php';
@mkdir(dirname($outPath), 0777, true);
file_put_contents($outPath, $code);

echo "Generated model: {$outPath}\n";
echo "Class name: {$class}\n";
echo "Table: {$table}\n";
echo "Primary key: {$pk}\n";
echo "Fillable fields: " . implode(', ', $fillable) . "\n";
