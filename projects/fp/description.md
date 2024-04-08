# Administrator Dashboard Enhancement (Final Project)

In this project, you will enhance the Administrator Dashboard in your WEB 3400 Server Side Programming Class. You will add Key Performance Indicators (KPIs), a quick article and ticket creation form, and a display of recent contact messages. This project will help you develop skills in SQL queries, PHP data handling, and dynamic content generation in HTML.

## Starter File: `admin_dashboard.php`

Your starter file should look like this:

```php
<?php include 'config.php'; ?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>
<!-- BEGIN YOUR CONTENT -->
<!-- END YOUR CONTENT -->
<?php include 'templates/footer.php'; ?>
```

## Completed File Overview

Your completed file will include several enhancements:

- Security checks to ensure only admin users can access the dashboard.
- SQL queries to fetch KPIs related to articles, tickets, and users.
- Display of KPIs in a visually appealing manner using Bulma CSS framework.
- Forms for quick addition of articles and tickets.
- A section to display the most recent contact messages.

## Step-by-Step Instructions

### 1. Add Security Check

At the top of your `admin_dashboard.php` file, add a security check to ensure only logged-in admin users can access the page.

```php
<?php
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}
?>
```

### 2. Define KPI Queries

Define an associative array `$kpiQueries` to store your SQL queries for various KPIs.

```php
$kpiQueries = [
    'total_articles_count' => 'SELECT COUNT(*) FROM articles',
    'published_articles_count' => 'SELECT COUNT(*) FROM articles WHERE is_published = 1',
    // Add more KPI queries here
];
```

### 3. Execute KPI Queries and Store Results

Loop through the `$kpiQueries` array, execute each query, and store the results in a new array `$kpiResults`.

```php
$kpiResults = [];
foreach ($kpiQueries as $kpi => $query) {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kpiResults[$kpi] = $stmt->fetchColumn();
}
```

### 4. Display KPIs in the Dashboard

In the HTML content section, use Bulma CSS classes to display the KPIs in a visually appealing layout.

```html
<section class="section">
    <div class="columns is-multiline">
        <div class="column is-4">
            <div class="box">
                <div class="heading">Total Articles</div>
                <div class="title"><?= $kpiResults['total_articles_count'] ?></div>
            </div>
        </div>
        <div class="column is-4">
            <div class="box">
                <div class="heading">Published Articles</div>
                <div class="title"><?= $kpiResults['published_articles_count'] ?></div>
            </div>
        </div>
        <!-- Add more KPI boxes here -->
    </div>
</section>
```

### 5. Add Quick Article and Ticket Creation Forms

Below the KPIs, add forms for quick article and ticket creation. Ensure these forms submit to the appropriate handling scripts (e.g., `article_add.php` and `ticket_create.php`).

```html
<div class="columns">
    <div class="column is-6">
        <!-- Quick Article Add Form -->
    </div>
    <div class="column is-6">
        <!-- Quick Ticket Add Form -->
    </div>
</div>
```

### 6. Display Recent Contact Messages

Query the database for the most recent contact messages and display them in a table.

```php
$stmt = $pdo->prepare('SELECT * FROM contact_us ORDER BY submitted_at DESC LIMIT 5');
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

```html
<div class="panel is-success">
    <p class="panel-heading">Contact Us Messages</p>
    <div class="panel-block">
        <table class="table">
            <!-- Table headers and rows for messages -->
        </table>
    </div>
</div>
```

### 7. Finalize and Test

- Ensure all files are correctly added and committed to your repository.
- Test your dashboard thoroughly to catch and fix any bugs or issues.
- Push your final changes to GitHub and submit your project URL as instructed.

## Conclusion

By completing this project, you will have enhanced your Administrator Dashboard with useful KPIs and added functionality for quick article and ticket creation. This will provide a more comprehensive overview of your
