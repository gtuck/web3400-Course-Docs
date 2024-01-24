# Introduction to Bulma CSS Framework

We'll follow a similar pattern as the previous HTML and CSS sections to introduce the Bulma CSS Framework. However to assure we are all starting from the same place I have provided a starter.html file for you to work from. Each section will focus on a specific aspect of Bulma, showing how to use its features in a project. This approach will help you understand how to integrate Bulma into your web development workflow. Bulma is a modern CSS framework based on Flexbox, offering a clean and easy-to-use interface for styling web pages. 

### Sync fork and update local repo.
- **Steps**:
  1. Navigate to your GitHub repository page for your fork of my web3400 repo.
  2. Run the "Sync fork" process, this will copy my `03/starter.html` folder/file into your GitHub repo assignments folder.
  3. Use GitHub Desktop (or the VSCode Terminal `git pull`) to 'pull' your repo, updating your local copy.

You should now have a `03` folder in your assignments directory that includes the `starter.html` file.

### Getting Started with Bulma
- **Objective**: Introduction to Bulma and setting it up in a project.
- **Topics**:
  1. What is Bulma and why use it?
  2. Download and add the starter.html file to your assignment 03 folder (via a Sync)
  3. Make a copy of the starter.html file and name it index.html (all updates will be to)
  4. Including Bulma, BulmaJS and FontAwesome in your HTML (using CDN).
  5. Basic Bulma Syntax and Classes.
- **Project**: Update the basic HTML page to include Bulma's CSS.

### Linking to Bulma CSS, BulmaJS and FontAwesome
Before we begin styling the HTML elements, we need to include the Bulma CSS, BulmaJS and FontAwesome files in our HTML. Add the following lines in the `<head>` section of your HTML document:

```html
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0/dist/bulma.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
```

These lines import the Bulma CSS & BulmaJS from CDNs, making all the Bulma styles available for our use and adding functionality to some of the componants.

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

### Styling the `<main>` and child elements
Bulma provides a grid system based on Flexbox. Let's apply it to the main content area:

```html
<main class="section">
    <div class="container">
        <h2 class="title is-2">
            This is the page title
        </h2>
        <p class="subtitle">
          Page content can go here....  
        </p>

        <h2 class="title">Working with tables</h2>
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th>Header 1</th>
                        <th>Header 2</th>
                        <th>Header 3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Data 1</td>
                        <td>Data 2</td>
                        <td>Data 3</td>
                    </tr>
                    <tr>
                        <td>Data 1</td>
                        <td>Data 2</td>
                        <td>Data 3</td>
                    </tr>
                    <tr>
                        <td>Data 1</td>
                        <td>Data 2</td>
                        <td>Data 3</td>
                    </tr>
                    <tr>
                        <td>Data 1</td>
                        <td>Data 2</td>
                        <td>Data 3</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Footer 1</th>
                        <th>Footer 2</th>
                        <th>Footer 3</th>
                    </tr>
                </tfoot>
            </table>

        <h2 class="title">Working with forms</h2>
            <form action="" method="post">
                <div class="field">
                    <label class="label">Name</label>
                    <div class="control">
                        <input class="input" type="text" placeholder="Your name">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Message</label>
                    <div class="control">
                        <textarea class="textarea" placeholder="Your message"></textarea>
                    </div>
                </div>
                <div class="control">
                    <button class="button is-primary">Submit</button>
                </div>
            </form>
    </div>
</main>
```

The `section` class adds spacing around your content, and `container` centers and constrains the width of your content. `content` and `menu` are utility classes for styling typical article and sidebar content.

### Adding content to the page `<section>` (Tables and Forms)
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