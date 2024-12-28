# PHP Template System Assignment

Creating a basic PHP template system is a great way to structure your web application for easier maintenance and scalability. By splitting your HTML into reusable components, you can manage your code more effectively. This assignment guides you through creating a simple PHP template system with `head.php`, `nav.php`, and `footer.php`, using the `index.html` file from the previous assignment as a starting point.

---

## Step 1: Copy Assignment 03 to Assignment 04
1. In a VS Code Terminal, navigate to your `assignments` folder:
   ```bash
   cd assignments
   ```
2. Recursively copy the folder:
   ```bash
   cp -r 03 04
   ```
3. Add, commit, and push the new folder:
   - Stage: 
     ```bash
     git add 04
     ```
   - Commit:
     ```bash
     git commit -m "Created assignment 04 folder"
     ```
   - Push:
     ```bash
     git push
     ```

---

## Step 2: Set Up Assignment 04
1. In the `04` directory, create a `templates` folder.
2. Inside `templates`, create three files: `head.php`, `nav.php`, and `footer.php`.

---

## Step 3: Create the Template Files

### `head.php`
Extract the `<head>` section from your `index.html` and include:
- Meta tags
- Stylesheets
- Scripts

Example content:
```php
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0/dist/bulma.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
    <title>My Webpage with Bulma</title>
</head>

<body class="has-navbar-fixed-top">
```

---

### `nav.php`
Extract the navigation and hero sections (e.g., `<nav>` and `<section class="hero">`) and include them in this file.

Example:
```php
<nav class="navbar is-fixed-top is-spaced has-shadow is-light">
    <div class="navbar-brand">
        <a class="navbar-item" href="#">
            <span class="icon-text">
                <span class="icon">
                    <i class="fas fa-2x fa-yin-yang"></i>
                </span>
                &nbsp;The Brand
            </span>
        </a>
    </div>
</nav>
```

---

### `footer.php`
Extract the `<footer>` section and include it in this file.

Example:
```php
<footer class="footer">
    <div class="content has-text-centered">
        <p>
            <strong>My Webpage</strong> by <a href="#">Your Name</a>. The source code is licensed under MIT.
        </p>
    </div>
</footer>
</body>
</html>
```

---

## Step 4: Use the Template Files
1. Delete the `index.html` file in the `04` folder.
2. Create a new `index.php` file and include the template files:
   ```php
   <?php include 'templates/head.php'; ?>
   <?php include 'templates/nav.php'; ?>

   <!-- Add your main content here -->

   <?php include 'templates/footer.php'; ?>
   ```

---

## Step 5: Stage, Commit, and Push Your Changes
1. Stage:
   ```bash
   git add 04
   ```
2. Commit:
   ```bash
   git commit -m "Updated assignment 04"
   ```
3. Push:
   ```bash
   git push
   ```

---

## Step 6: Submit the Assignment
1. Submit the URL to your `04` folder in the following format:
   ```
   https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/04/
   ```
   Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.
