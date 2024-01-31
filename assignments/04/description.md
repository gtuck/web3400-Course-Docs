# Introduction to PHP templates (Assignment 04)

Creating a basic PHP template system is the next step in our development journey and a fantastic way to structure your web application for easy maintenance and scalability. Splitting your HTML structure into reusable components allows you to manage your code more effectively. Let's use the `index.html` file from the previous assignment to guide the creation of a simple PHP template system consisting of `head.php`, `nav.php`, and `footer.php`.

## Copy Assignment 03 to a new Assignment 04 folder.

1. In a VS Code Terminal, `cd` to your `assignments` folder.
2. Recursively copy your folder: Run `cp -r 03 04`.
3. Add, commit, and push the new `04` folder to your repo.
   a. Stage the Change: Execute `git add 04`.
   b. Commit the Change: Type `git commit -m "Created assignment 04 folder"`.
   c. Push the Change: Run `git push`.

## Setting Up Assignment 04

1. In your assignments folder, create a directory `04`.
2. Inside this directory, create a `templates` folder.
3. Inside the `templates` folder, create three files: `head.php`, `nav.php`, and `footer.php`.

## Creating the Template Files

### head.php

Extract the `<head>` section from your index.html file and place it inside `head.php`. This file will include all necessary meta tags, stylesheets, and scripts.

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

### nav.php

Extract the navigation (`<nav>`) and hero (`<section class="hero">`) sections and place them in `nav.php`. This file will generate the navigation bar and any hero sections on your page.

```php
<!-- BEGIN PAGE HEADER -->
    <header class="container">

        <!-- BEGIN MAIN NAV -->
        <nav class="navbar is-fixed-top is-spaced has-shadow is-light" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="#">
                    <span class="icon-text">
                        <span class="icon">
                            <i class="fas fa-2x fa-yin-yang"></i>
                        </span>
                        <span>&nbsp;The Brand</span>
                    </span>
                </a>
                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            <div class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="#">Home</a>
                    <a class="navbar-item" href="#">About</a>
                </div>
                <div class="navbar-end">
                    <!-- <a class="navbar-item" href="#">Contact Us</a>
                    <a class="navbar-item" href="#">Login</a> -->
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-light">
                                <strong>Contact us</strong>
                            </a>
                            <a class="button is-link">
                                Log in
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- END MAIN NAV -->
        <section class="block">
            &nbsp; <!-- this adds a little extra space between the nav and the hero -->
        </section>
        <!-- BEGIN HERO 
        <section class="hero is-primary">
            <div class="hero-body">
                <p class="title">
                    Hero title
                </p>
                <p class="subtitle">
                    Hero subtitle
                </p>
            </div>
        </section>
         END HERO -->

    </header>
    <!-- END PAGE HEADER -->

    <!-- BEGIN MAIN PAGE CONTENT -->
    <main class="container">
```

### footer.php

Extract the `<footer>` section and place it into `footer.php`. This will contain the footer content for your website.

```php
</main>
    <!-- END MAIN PAGE CONTENT -->

    <!-- BEGIN PAGE FOOTER -->
    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                <strong>My Webpage</strong> by <a href="#">Your Name</a>. The source code is licensed under MIT.
            </p>
        </div>
    </footer>
    <!-- END PAGE FOOTER -->

</body>

</html>
```

## Using the Template Files

First, delete the `index.html` file in your assignment `04` folder. Then, to use our new templates, we'll use the PHP `include` statement. Create a new page named `index.php` in your `04` folder and add the following code:

```php
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

    <!-- BEGIN YOUR CONTENT -->

    <!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
```

## Conclusion

This basic template system in PHP promotes reusability and maintainability, making your web development process more efficient and your application more straightforward to manage. This allows you to change elements in one place (like adding a new CSS file in `head.php` or a new navigation link in `nav.php`) and have those changes reflected across all pages of your site that use these templates. As your website grows, you might need to create more templates (e.g., for a sidebar, special sections, etc.). Create new `.php` files in your `templates` directory for these components and include them in your pages as needed.

## Stage, Commit, and Push the Final Changes
- **Objective**: Commit and push your completed index.html file in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add 04`.
  2. Commit the Changes: Type `git commit -m "Update assignment 04"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Assignment
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated assignment `04` folder in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/04/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.
