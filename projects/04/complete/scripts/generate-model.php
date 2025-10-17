#!/usr/bin/env php
<?php
// scripts/generate-model.php
// Usage: php scripts/generate-model.php <table_name>

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// Load .env so Database::pdo() has credentials
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

use App\Support\Database;
use PDO;

[$script, $table] = $argv + [null, null];
if (!$table) {
    fwrite(STDERR, "Usage: php scripts/generate-model.php <table>\n");
    exit(1);
}

$pdo = Database::pdo();

$sql = "SELECT COLUMN_NAME, COLUMN_KEY\n          FROM INFORMATION_SCHEMA.COLUMNS\n         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t\n      ORDER BY ORDINAL_POSITION";
$stmt = $pdo->prepare($sql);
$stmt->execute([':t' => $table]);
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cols) {
    fwrite(STDERR, "Table not found: {$table}\n");
    exit(1);
}

$pk = 'id';
$fillable = [];
foreach ($cols as $col) {
    $name = $col['COLUMN_NAME'];
    if ($col['COLUMN_KEY'] === 'PRI') {
        $pk = $name;
        continue;
    }
    if (in_array($name, ['created_at', 'updated_at', 'deleted_at'], true)) {
        continue;
    }
    $fillable[] = $name;
}

function studly(string $s): string
{
    $s = str_replace(['-', '_'], ' ', $s);
    $s = ucwords($s);
    return str_replace(' ', '', $s);
}

function ends_with(string $s, string $suffix): bool
{
    $len = strlen($suffix);
    if ($len === 0) return true;
    return substr($s, -$len) === $suffix;
}

function singular(string $s): string
{
    if (ends_with($s, 'ies')) return substr($s, 0, -3) . 'y';
    if (ends_with($s, 'ses')) return substr($s, 0, -2); // e.g., analyses -> analysis
    if (ends_with($s, 's')) return substr($s, 0, -1);
    return $s;
}

$class = studly(singular($table));

$template = <<<'PHP'
<?php
namespace App\Models;

final class %CLASS% extends BaseModel
{
    protected static string $table = '%TABLE%';
    protected static string $primaryKey = '%PK%';
    protected static array $fillable = %FILLABLE%;
}

PHP;

$code = str_replace(
    ['%CLASS%', '%TABLE%', '%PK%', '%FILLABLE%'],
    [$class, $table, $pk, var_export($fillable, true)],
    $template
);

$outPath = dirname(__DIR__) . '/src/Models/' . $class . '.php';
@mkdir(dirname($outPath), 0777, true);
file_put_contents($outPath, $code);

echo "Generated model: {$outPath}\n";
