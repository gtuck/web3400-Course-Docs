# CSS Frameworks (Bulma)

We'll follow a similar pattern as the previous HTML and CSS sections to introduce the Bulma CSS Framework. Each section will focus on a specific aspect of Bulma, showing how to use its features in a project. This approach will help you understand how to integrate Bulma into your web development workflow. Bulma is a modern CSS framework based on Flexbox, offering a clean and easy-to-use interface for styling web pages.

### Copy/Clone Assignment 02 to a new Assignment 03 folder. 
- **Steps**:
  1. In a VS Code Terminal, `cd` to your `assignments` folder.
  2. Recursively copy your folder: Run `cp -r 02 03`.
  3. Add, commit, and push the new `03` folder to your repo.
     1. Stage the Change: Execute `git add 03`.
     2. Commit the Change: Type `git commit -m "Created assignment 03"`.
     3. Push the Change: Run `git push`.


### Linking Bulma CSS & BulmaJS
Before we begin styling the HTML elements, we need to include the Bulma CSS & BulmaJS files in our HTML. Add the following lines in the `<head>` section of your HTML document:

```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css">
<script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0/dist/bulma.min.js"></script>
```

This line imports the Bulma CSS & BulmaJS from a CDN, making all the Bulma styles available for our use and adding functionality to some of the componants.

### Styling the `<header>`
Bulma provides classes for navigation, typography, and containers. Let's start by styling the `<header>`:

```html
<header class="hero is-primary">
    <div class="hero-body">
        <p class="title">
            Welcome to HTML and CSS!
        </p>
    </div>
</header>
```

Here, we use the `hero` class for a full-width banner and `is-primary` to apply a primary color. The `hero-body` class centers your content, and `title` gives a nice typographic style to your heading.

### Styling the `<nav>`
Bulma has a powerful and responsive navigation component:

```html
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="#">Home</a>
            <a class="navbar-item" href="#">About</a>
        </div>
        <div class="navbar-end">
            <a class="navbar-item" href="#">Join</a>
            <a class="navbar-item" href="#">Login</a>
        </div>
    </div>
</nav>
```

The `navbar` class creates a responsive navigation bar, with `navbar-menu` and `navbar-start` organizing your navigation items.

### Step 4: Styling the `<main>` and Its Children
Bulma provides a grid system based on Flexbox. Let's apply it to the main content area:

```html
<main class="section">
    <div class="container">
        <article class="content">
            <!-- Article content here -->
        </article>
        <aside class="menu">
            <!-- Aside content here -->
        </aside>
    </div>
</main>
```

The `section` class adds spacing around your content, and `container` centers and constrains the width of your content. `content` and `menu` are utility classes for styling typical article and sidebar content.

### Step 5: Styling `<section>` (Tables and Forms)
Bulma has specific classes for tables and form elements:

```html
<section class="section">
    <table class="table">
        <!-- Table content here -->
    </table>

    <form>
        <!-- Form content with Bulma form classes -->
    </form>
</section>
```

The `table` class styles your tables with Bulma's design, and you can use various form classes (`input`, `textarea`, `button`) to style each form element.

### Step 6: Styling the `<footer>`
Lastly, let's style the footer:

```html
<footer class="footer">
    <div class="content has-text-centered">
        <p>
            Copyright © 2024
        </p>
    </div>
</footer>
```

The `footer` class adds a footer with padding, and `has-text-centered` centers your text content.

### Conclusion
By following these steps, you can apply Bulma styles to your HTML structure, making it more visually appealing and responsive. Remember, Bulma is highly customizable, so feel free to explore its documentation for more advanced styling options. Happy coding!