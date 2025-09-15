# Assignment 04: PHP Template System (Head / Nav / Footer Includes)

Purpose: Introduce a lightweight PHP templating approach that reduces repetition, improves maintainability, and sets the stage for future dynamic pages.

## Why a Template System?
As pages grow, repeating boilerplate (doctype, head tags, navigation, footer) increases risk of:
- Inconsistency (forgetting to update every page)
- Larger diffs / merge conflicts
- Slower iteration

A simple include-based template system:
- Centralizes shared markup (one change updates all pages)
- Encourages semantic, modular structure
- Low cognitive load (pure PHP includes; no framework required)
- Scales into more advanced patterns later (layouts, partials, components)

Common industry practice: Even full frameworks (Laravel, Rails, Django) formalize this idea with layout and partial templates. You are building the minimal foundation manually to understand the concept.

## Learning Objectives
You can:
1. Copy prior assignment (A03) into a new folder `assignments/04`.
2. Create a `templates/` directory with `head.php`, `nav.php`, and `footer.php`.
3. Extract and relocate shared markup cleanly (no duplication, correct tag balance).
4. Use `include` / `require` to assemble `index.php`.
5. Pass a per-page title variable into the head template.
6. Remove the old `index.html` and rely on `index.php`.
7. Commit and push changes; submit the correct file URL.

## Prerequisites
- Repository: `web3400-fall25`
- A00–A03 complete
- Dev Container or Codespace running (PHP available)
- Instructor collaborator (`gtuck`) already invited

---

## Step 0. Copy Assignment 03 to 04
From repository root:

```bash
cd assignments
cp -R 03 04   # Recursive copy of prior assignment
```

Stage & commit the new folder:

```bash
git add assignments/04
git commit -m "A04: copy A03 to start PHP templating assignment"
git push
```

Do NOT edit A03; treat 04 as the working copy.

---

## Step 1. Create Template Directory & Files
Inside `assignments/04`:

```bash
cd assignments/04
mkdir templates
cd templates
touch head.php nav.php footer.php
```

Delete `index.html` (it will be replaced):

```bash
rm index.html
```

---

## Step 2. Build `head.php`
Purpose: Provide the document start, metadata, linked CSS/JS, open `<body>`.

Add a dynamic page title variable fallback:

```php
<?php
// filepath: assignments/04/templates/head.php
$pageTitle = $pageTitle ?? 'Site Title';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Assignment 04 - PHP templating practice">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Bulma & Assets (same versions used in A03 for consistency) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script>
</head>
<body class="has-navbar-fixed-top">
```

Notes:
- No closing `</body>` or `</html>` here (footer closes them).
- Use `htmlspecialchars` to avoid accidental HTML injection in title.

---

## Step 3. Build `nav.php`
Extract ONLY navigation / hero (if used). Keep semantics; add brand or simple links:

```php
<?php
// filepath: assignments/04/templates/nav.php
?>
<!-- BEGIN PAGE HEADER -->
<header class="container">

    <!-- BEGIN MAIN NAV -->
    <nav class="navbar is-fixed-top is-spaced has-shadow is-light" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
                <span class="icon-text">
                    <span class="icon">
                        <i class="fas fa-code"></i>
                    </span>
                    <span>Tractors</span>
                </span>
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <!-- Admin menu will go here in the future -->
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <a class="button">Contact us</a>
                        <a class="button">Log in</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- END MAIN NAV -->
    <section class="block">&nbsp;</section>
    <!-- BEGIN HERO -->
    <section class="hero is-primary">
        <div class="hero-body">
            <p class="title">Hero title</p>
            <p class="subtitle">Hero subtitle</p>
        </div>
    </section>
    <!-- END HERO -->
</header>
<!-- END PAGE HEADER -->
<!-- BEGIN MAIN PAGE CONTENT -->
<main class="container">
```

(Adjust links if you add more pages.)

---

## Step 4. Build `footer.php`
Close structural tags and document:

```php
<?php
// filepath: assignments/04/templates/footer.php
$year = date('Y');
?>
  </main>
    <!-- END MAIN PAGE CONTENT -->
    <!-- BEGIN PAGE FOOTER -->
    <footer class="footer">
       <div class="content has-text-centered">
           <p>
              <p>&copy; <?= $year ?> Your Name. Built with Bulma & PHP.</p>
           </p>
       </div>
   </footer>
 <!-- END PAGE FOOTER -->
</body>
</html>
```

---

## Step 5. Create `index.php`
Reassemble page using includes. Keep your main content (tables, form, columns, cards) inside `<main>` or appropriate Bulma sections.

```php
<?php
// filepath: assignments/04/index.php
$pageTitle = 'Home - A04 Templates';
?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<h1 class="title">This a title.</h1>
<h2 class="subtitle">This is a subtitle</h2>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>
```

---

## Step 6. Test Locally (Optional Built-in Server)
From repository root:

```bash
php -S 0.0.0.0:8080
```

Browse: http://localhost:8080/assignments/04/index.php  
(Or use forwarded port in Codespaces / Dev Container.)

---

## Step 7. Stage, Commit, Push
```bash
git add assignments/04
git commit -m "A04: implement PHP template includes (head, nav, footer)"
git push origin main
```

---

## Submission
Submit direct URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/assignments/04/index.php
```

Open in a private/incognito window to confirm accessibility.

---

## Self-Checklist
- Folder: `assignments/04/`
- Deleted old `index.html`
- Files present:
  - `index.php`
  - `templates/head.php`
  - `templates/nav.php`
  - `templates/footer.php`
- No duplicate DOCTYPE or `<html>` tags
- `$pageTitle` used (dynamic title renders)
- Includes in correct order (head → nav → content → footer)
- Content from A03 adapted into main area
- Semantic structure preserved
- Commit pushed; URL correct

---

## Common Mistakes
- Leaving `index.html` (confusing which page to load)
- Duplicating closing `</html>` in multiple templates
- Forgetting to set `$pageTitle` before including `head.php`
- Using wrong relative paths (all includes are relative to the executing script)
- Editing Bulma CDN version inconsistently
- Large single “finished A04” commit instead of meaningful steps
- Not escaping dynamic values (title) (we used `htmlspecialchars`)

---

## Support
Questions: tag [A04]. Include screenshot + error message (if any).  
Office hours: see syllabus.

## Academic Integrity
Write your own template extraction. Discuss approach with peers; do not copy someone else’s repository
