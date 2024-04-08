# Comprehensive Final Project: Administrator Dashboard Enhancement

The final project for the WEB 3400 Server Side Programming Class is designed to be a comprehensive culmination of all the skills and knowledge you've acquired throughout the course. This project involves enhancing the Administrator Dashboard with new features and ensuring that all functionalities from previous projects are integrated and functioning correctly.

## Key Aspects of the Final Project:

### 1. Integration of Previous Features:
Your final project should seamlessly incorporate all the features you've developed in previous projects. This includes user authentication, article management, ticketing systems, and other functionalities you've implemented. These features must work together harmoniously in the enhanced dashboard.

### 2. New Features and Enhancements:
In addition to the existing features, you'll add new functionalities such as Key Performance Indicators (KPIs) for articles, tickets, and users, quick creation forms for articles and tickets, and a section to display recent contact messages. These enhancements should provide a more comprehensive and user-friendly experience for administrators.

### 3. Comprehensive Functionality:
The final project is not just about adding new features but ensuring that the entire dashboard operates as a cohesive unit. All features, both old and new, must function correctly and efficiently. This includes proper data retrieval and display, error handling, security measures, and user interaction.

### 4. Quality Assurance:
Thorough testing is essential to ensure that every dashboard aspect works as intended. This includes testing individual features, their integration, and the overall user experience. Any bugs or issues should be identified and resolved before the final submission.

### 5. Professional Presentation:
The presentation of your final project should reflect the professionalism expected in a real-world application. This includes a clean and intuitive user interface, consistent styling, and clear documentation of your code and functionalities.

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
- Display KPIs in a visually appealing manner using the Bulma CSS framework.
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

Bulma CSS classes display KPIs in the HTML content section in a visually appealing layout. See an example finished admin_dashboard.php page at **(please do not add, modify, or delete any site data)**:
- https://garthtuck.com/web3400/fp/admin_dashboard.php 
- Username: admin@admin.com and 
- Password: password

```html
<section class="section">
    <div class="columns is-multiline">
        <div class="column">
                <div class="box">
                    <div class="heading"><a href="articles.php">Articles</a></div>
                    <div class="title">Count: <?= $kpiResults["total_articles_count"] ?></div>
                    <div class="level">
                        <div class="level-item">
                            <div class="">
                                <div class="heading">Unublished</div>
                                <div class="title is-5"><?= $kpiResults["unpublished_articles_count"] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div class="">
                                <div class="heading">Published</div>
                                <div class="title is-5"><?= $kpiResults["published_articles_count"] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div class="">
                                <div class="heading">Featured</div>
                                <div class="title is-5"><?= $kpiResults["featured_articles_count"] ?></div>
                            </div>
                        </div>
                    </div>
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

## Conclusion:

The final project is an opportunity to showcase your full range of skills in server-side programming. It's a testament to your ability to create a comprehensive, functional, professional web application. Ensuring that all features from previous projects function correctly in conjunction with the new enhancements is key to the success of your final dashboard.
