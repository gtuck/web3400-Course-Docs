<?php
/**
 * Database Class
 *
 * PURPOSE:
 * Provides a centralized factory method for creating PDO (PHP Data Objects) connections.
 * Ensures consistent database configuration and security settings across the application.
 *
 * RESPONSIBILITIES:
 * - Read database credentials from environment variables
 * - Create properly configured PDO instances
 * - Set secure PDO attributes (exception mode, fetch mode, prepared statements)
 * - Support dependency injection and testing
 *
 * SECURITY FEATURES:
 * - No hardcoded credentials (uses environment variables)
 * - True prepared statements (EMULATE_PREPARES = false)
 * - Exception error mode for proper error handling
 * - UTF-8 charset to prevent encoding issues
 *
 * USAGE EXAMPLE:
 * ```php
 * // In BaseModel or any class needing database access
 * $pdo = Database::pdo();
 * $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
 * $stmt->execute([':id' => $userId]);
 * $user = $stmt->fetch();
 * ```
 */

namespace App\Support;

class Database
{
    /**
     * Create and return a configured PDO instance
     *
     * This factory method creates a PDO connection with secure defaults.
     * It reads all configuration from environment variables to avoid
     * hardcoding sensitive credentials in the codebase.
     *
     * REQUIRED ENVIRONMENT VARIABLES:
     * - DB_HOST: Database server hostname (e.g., 'localhost', '127.0.0.1')
     * - DB_NAME: Database name (e.g., 'my_app_db')
     * - DB_USER: Database username (e.g., 'root', 'app_user')
     * - DB_PASS: Database password
     * - DB_CHARSET: Character encoding (typically 'utf8mb4')
     *
     * PDO CONFIGURATION:
     * - ERRMODE_EXCEPTION: Throws PDOException on errors (easier error handling)
     * - FETCH_ASSOC: Returns rows as associative arrays by default
     * - EMULATE_PREPARES = false: Uses real prepared statements (more secure)
     *
     * @return \PDO Configured PDO instance ready for queries
     *
     * @throws \PDOException If connection fails or credentials are invalid
     *
     * EXAMPLE:
     * ```php
     * try {
     *     $pdo = Database::pdo();
     *     $stmt = $pdo->query('SELECT COUNT(*) FROM users');
     *     $count = $stmt->fetchColumn();
     *     echo "Total users: {$count}";
     * } catch (\PDOException $e) {
     *     error_log("Database error: " . $e->getMessage());
     * }
     * ```
     *
     * SECURITY NOTES:
     * - Never echo PDOException messages to users (may contain sensitive info)
     * - Always use prepared statements for user input
     * - Environment variables should be loaded via Dotenv in production
     * - EMULATE_PREPARES = false prevents SQL injection via type juggling
     */
    public static function pdo(): \PDO
    {
        // Read all required settings from environment variables (no hardcoded defaults)
        $host    = $_ENV['DB_HOST'];
        $name    = $_ENV['DB_NAME'];
        $user    = $_ENV['DB_USER'];
        $pass    = $_ENV['DB_PASS'];
        $charset = $_ENV['DB_CHARSET'];

        // Build MySQL DSN (Data Source Name)
        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

        // Create and return PDO with secure configuration
        return new \PDO($dsn, $user, $pass, [
            // Throw exceptions on errors (instead of silent failures)
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,

            // Return associative arrays by default (easier to work with)
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,

            // Use real prepared statements (more secure, prevents type juggling attacks)
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
