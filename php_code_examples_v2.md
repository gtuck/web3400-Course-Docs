# PHP Code Examples

This document is designed as a detailed resource. It provides topic descriptions, explanations, and PHP code examples.

---

## Table of Contents

1. [Commenting Code](#commenting-code)
2. [Basic Data Types](#basic-data-types)
3. [Variables](#variables)
4. [Assignment Operators](#assignment-operators)
5. [Comparison Operators](#comparison-operators)
6. [Logical Operators](#logical-operators)
7. [Strings](#strings)
8. [Numbers](#numbers)
9. [Arrays](#arrays)
10. [Dates](#dates)
11. [Control Statements](#control-statements)
12. [Alternative Syntax for Control Structures](#alternative-syntax-for-control-structures)
13. [Ternary Operator](#ternary-operator)
14. [Switch Statement](#switch-statement)
15. [`header()`](#header-function)[ Function](#header-function)
16. [`include`](#include-and-require-statements)[ and ](#include-and-require-statements)[`require`](#include-and-require-statements)[ Statements](#include-and-require-statements)
17. [Database Connectivity (PDO)](#database-connectivity-pdo)
18. [Database CRUD Operations](#database-crud-operations)
19. [Displaying Records in HTML](#displaying-records-in-html)
20. [Other Database Operations](#other-database-operations)
21. [GET and POST Requests](#get-and-post-requests)
22. [HTML Forms](#html-forms)
23. [Password Hashing](#password-hashing)
24. [Sessions](#sessions)
25. [Cookies](#cookies)
26. [JSON Data](#json-data)

---

## Commenting Code

PHP supports single-line (`//` or `#`), multi-line (`/* ... */`), and documentation (`/** ... */`) comments.

```php
// Single-line comment
echo "Hello"; // Inline comment

# Another single-line comment

/* Multi-line
   comment */
echo "World";

/**
 * Documentation comment
 * @param string $name
 */
function greet($name) {
    echo "Hello, $name";
}
```

---

## Basic Data Types

PHP supports several data types including string, integer, float, boolean, array, object, and null.

```php
$text = "PHP";     // String
$number = 42;       // Integer
$price = 19.99;     // Float
$isValid = true;    // Boolean
$colors = ["Red", "Blue"]; // Array

var_dump($text);    // Shows type and value
```

---

## Variables

Variables are prefixed with `$` and store values.

```php
$name = "John";
echo "Hello, $name"; // Outputs: Hello, John

$a = 10;
$b = 20;
$sum = $a + $b; // 30

$fruits = ["Apple", "Banana"];
echo $fruits[0]; // Apple
```

---

## Assignment Operators

Assignment operators assign values or combine assignment with an operation.

```php
$a = 10;
$a += 5; // 15
$a -= 2; // 13
$a *= 2; // 26
$a /= 2; // 13
$a %= 3; // 1

$text = "Hello";
$text .= " World"; // Hello World
```

---

## Comparison Operators

Used to compare values.

```php
if (3 == "3") echo "Equal values";   // true
if (3 === "3") echo "Identical";     // false
if (5 > 3) echo "Greater";           // true
if (2 < 3) echo "Less";               // true
```

---

## Logical Operators

Combine conditions.

```php
$age = 25;
$isEmployed = true;

if ($age > 18 && $isEmployed) echo "Eligible";
if ($age > 18 || $isEmployed) echo "One condition is true";
if (!$isEmployed) echo "Not employed";
```

---

## Strings

Strings hold text data.

```php
$greeting = "Hello, World!";
$fullName = "John" . " " . "Doe"; // Concatenation

$length = strlen($greeting);  // 13
$upper = strtoupper($greeting); // HELLO, WORLD!
$sub = substr($greeting, 7, 5); // World
```

---

## Numbers

Working with integers and floats.

```php
$x = 10;
$y = 20.5;

$sum = $x + $y; // 30.5
$product = $x * 2; // 20
$rounded = round($y); // 21
$random = rand(1, 100);
```

---

## Arrays

Arrays can be indexed or associative.

```php
$fruits = ["Apple", "Banana"];
echo $fruits[0]; // Apple

$ages = ["Peter" => 20, "John" => 30];
echo $ages["John"]; // 30

foreach ($fruits as $fruit) {
    echo $fruit . "\n";
}
```

---

## Dates

Format and display dates and times.

```php
echo date("Y-m-d"); // Current date
echo date("H:i:s"); // Current time

$timestamp = 1672915200;
echo date("F j, Y", $timestamp); // January 5, 2023
```

---

## Control Statements

Conditional branching with `if`, `else`, and `elseif`.

```php
$score = 75;

if ($score >= 90) echo "A";
elseif ($score >= 80) echo "B";
elseif ($score >= 70) echo "C";
else echo "F";
```

---

## Alternative Syntax for Control Structures

Cleaner syntax when mixing PHP and HTML.

```php
<?php $time = date("H"); ?>

<?php if ($time < 12): ?>
  <p>Good morning</p>
<?php elseif ($time < 18): ?>
  <p>Good afternoon</p>
<?php else: ?>
  <p>Good evening</p>
<?php endif; ?>
```

---

## Ternary Operator

Short form of `if-else`.

```php
$age = 20;
$isAdult = ($age >= 18) ? "Yes" : "No";
echo $isAdult; // Yes
```

---

## Switch Statement

Alternative to multiple `if` conditions.

```php
$day = 3;

switch ($day) {
  case 1: echo "Monday"; break;
  case 2: echo "Tuesday"; break;
  case 3: echo "Wednesday"; break;
  default: echo "Invalid";
}
```

---

## `header()` Function

Used for redirects.

```php
<?php
$loggedIn = false;
if (!$loggedIn) {
  header('Location: login.php');
  exit;
}
?>
```

---

## `include` and `require` Statements

Insert code from other files.

```php
include 'header.php';
require 'footer.php';
```

Use `require_once` and `include_once` to prevent multiple inclusions.

---

## Database Connectivity (PDO)

Connecting to a database using PDO.

```php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
```

---

## Database CRUD Operations

Performing Create, Read, Update, and Delete.

```php
// INSERT
$stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
$stmt->execute(['Alice']);

// SELECT
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$rows = $stmt->fetchAll();

// UPDATE
$stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute(['Bob', 1]);

// DELETE
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([1]);
```

---

## Displaying Records in HTML

Outputting database results.

```php
<table>
  <tr><th>Name</th><th>Email</th></tr>
  <?php foreach ($records as $r): ?>
    <tr><td><?= $r['name'] ?></td><td><?= $r['email'] ?></td></tr>
  <?php endforeach; ?>
</table>
```

---

## Other Database Operations

```php
echo $stmt->rowCount();
echo $pdo->lastInsertId();
```

---

## GET and POST Requests

Checking request parameters.

```php
if (isset($_GET['id'])) echo $_GET['id'];
if (isset($_POST['name'])) echo $_POST['name'];
```

---

## HTML Forms

Simple form and processing.

```html
<form method="post">
  Name: <input type="text" name="name">
  <input type="submit">
</form>
```

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  echo htmlspecialchars($_POST['name']);
}
```

---

## Password Hashing

Securely store and verify passwords.

```php
$hash = password_hash("mypassword", PASSWORD_DEFAULT);

if (password_verify("mypassword", $hash)) {
    echo "Valid password";
}
```

---

## Sessions

Maintain data across pages.

```php
session_start();
$_SESSION['user'] = "John";
echo $_SESSION['user'];

session_destroy();
```

---

## Cookies

Store data on the client.

```php
setcookie("user", "John", time()+3600, "/");
echo $_COOKIE['user'];
```

---

## JSON Data

Convert between PHP arrays and JSON.

```php
$data = ["name" => "John", "age" => 30];
$json = json_encode($data);

$decoded = json_decode($json, true);
echo $decoded['name'];
```
