# Administrator Dashboard Enhancement (Final Project)

In this project, you will enhance the Administrator Dashboard in your WEB 3400 Server Side Programming Class. You will add Key Performance Indicators (KPIs), a quick article and ticket creation form, and a display of recent contact messages. This project will help you develop skills in SQL queries, PHP data handling, and dynamic content generation in HTML.

## Copy Project 06 to the Final Project (fp) folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub

## Starter File: `admin_dashboard.php` (should already exist from a previous version)

Your starter file should look like this:

```php
<?php
// Step 1: Include config.php file
include 'config.php';

// Step 2: Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect user to login page or display an error message
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}
?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Admin Dashboard</h1>
    <p>Admin dashboard content will be created in a future project...</p>
</section>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
```

## Completed File Overview

Your completed file will include several enhancements:

- SQL queries to fetch KPIs related to articles, tickets, and users.
- Display of KPIs in a visually appealing manner using Bulma CSS framework.
- Forms for quick addition of articles and tickets.
- A section to display the most recent contact messages.

## Step-by-Step Instructions

### Define KPI Queries

Define an associative array `$kpiQueries` to store your SQL queries for various KPIs.

```php
// KPI Queries
$kpiQueries = [
    'total_articles_count' => 'SELECT COUNT(*) AS total_articles_count FROM articles',
    'unpublished_articles_count' => 'SELECT COUNT(*) AS unpublished_articles_count FROM articles WHERE is_published = 0',
    'published_articles_count' => 'SELECT COUNT(*) AS published_articles_count FROM articles WHERE is_published = 1',
    'featured_articles_count' => 'SELECT COUNT(*) AS featured_articles_count FROM articles WHERE is_featured = 1',
    'total_user_interactions' => 'SELECT COUNT(*) FROM `user_interactions`',
    'average_likes_per_article' => 'SELECT ROUND(AVG(likes_count), 2) AS average_likes_per_article FROM articles',
    'average_favs_per_article' => 'SELECT ROUND(AVG(favs_count), 2) AS average_favs_per_article FROM articles',
    'average_comments_per_article' => 'SELECT ROUND(AVG(comments_count), 2) AS average_comments_per_article FROM articles',
    'total_tickets_count' => 'SELECT COUNT(*) AS total_tickets_count FROM tickets',
    'open_tickets_count' => 'SELECT COUNT(*) AS open_tickets_count FROM tickets WHERE status = "Open"',
    'in_progress_tickets_count' => 'SELECT COUNT(*) AS open_tickets_count FROM tickets WHERE status = "In Progress"',
    'closed_tickets_count' => 'SELECT COUNT(*) AS closed_tickets_count FROM tickets WHERE status = "Closed"',
    'total_user_count' => 'SELECT COUNT(*) AS user_count FROM users WHERE role = "user"',
    'most_active_user' => "SELECT CONCAT(u.full_name, ': ', COUNT(ui.id), ' interactions') AS user_interactions FROM users u JOIN user_interactions ui ON u.id = ui.user_id WHERE u.role = 'user' GROUP BY u.full_name ORDER BY COUNT(ui.id) DESC LIMIT 1",
];
```

### Execute KPI Queries and Store Results

Loop through the `$kpiQueries` array, execute each query, and store the results in a new array `$kpiResults`.

```php
$kpiResults = [];
foreach ($kpiQueries as $kpi => $query) {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kpiResults[$kpi] = $stmt->fetchColumn();
}
```

### Display KPIs in the Dashboard

In the HTML content section, Bulma CSS classes display KPIs in a visually appealing layout. See an example finished admin_dashboard.php page at: 
https://garthtuck.com/web3400/fp/admin_dashboard.php 
Username: admin@admin.com and 
Password: password

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

### Add Quick Article and Ticket Creation Forms

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

### Display Recent Contact Messages

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

## Update the `nav.php` navigation template file

**nav.php**: Update the `Admin` menu to include the `Dashboard` link to the admin_dashboard.php page. The code should be added to the top of the `navbar-start` section of the main navbar.

```html
<!-- BEGIN ADMIN MENU -->
...
  <a href="admin_dashboard.php" class="navbar-item">
    Dashboard
  </a>
<!-- END ADMIN MENU -->
```

### Finalize and Test

- Ensure all files are correctly added and committed to your repository.
- Test your dashboard thoroughly to catch and fix any bugs or issues.
- Push your final changes to GitHub and submit your project URL as instructed.

## Conclusion

By completing this project, you will have enhanced your Administrator Dashboard with useful KPIs and added functionality for quick article and ticket creation. This will provide a more comprehensive overview of your
