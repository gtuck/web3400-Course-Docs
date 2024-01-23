# CSS Frameworks (Bulma)

We'll follow a similar pattern as the previous HTML and CSS sections to introduce the Bulma CSS Framework. Each section will focus on a specific aspect of Bulma, showing how to use its features in a project. This approach will help you understand how to integrate Bulma into your web development workflow.

## Copy/Clone Assignment 02 to a new Assignment 03 folder. 
- **Objective**: Understand how to use the VS Code Terminal to `cp` the previous assignment folder and all of its files to a new assignment folder.
- **Topics**:
  1. In a VS Code Terminal, `cd` to your `assignments` folder.
  2. Recursively copy your folder: Run `cp -r 02 03`.
  4. Add, commit, and push the new `03` folder to your repo.
     2. Stage the Change: Execute `git add 03`.
     3. Commit the Change: Type `git commit -m "Created assignment 03"`.
     4. Push the Change: Run `git push`.

## Introduction to Bulma CSS Framework

### Getting Started with Bulma
- **Objective**: Introduction to Bulma and setting it up in a project.
- **Topics**:
  1. What is Bulma and why use it?
  2. Including Bulma in your HTML (using CDN).
  3. Including BulmaJS in your HTML (using CDN).
  4. Basic Bulma Syntax and Classes.
- **Project**: Update the basic HTML page to include Bulma's CSS.

**HTML Example**:
```html
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0/dist/bulma.min.js"></script>
    <title>My Webpage with Bulma</title>
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Hello World</h1>
            <p class="subtitle">My first website with <strong>Bulma</strong>!</p>
        </div>
    </section>
</body>

</html>
```

### Typography and Helpers
- **Objective**: Learn how to use Bulma for typography and utility.
- **Topics**:
  1. Typography with Bulma (text alignment, size, transformation).
  2. Color Helpers (text color, background color).
  3. Spacing Helpers (margin, padding).
- **Project Update**: Apply Bulma typography and helper classes to the existing project.

**HTML Example**:
```html
<div class="content">
    <h2 class="title is-2 has-text-centered">Centered Title</h2>
    <p class="has-text-primary">This paragraph is primary-colored.</p>
    <p class="is-size-3">This text is size 3.</p>
    <p class="has-text-weight-bold">Bold text</p>
</div>
```
### Creating Layouts with Bulma
- **Objective**: Understand Bulma’s layout components.
- **Topics**:
  1. Bulma Container.
  2. Bulma Columns for Grid Layouts.
  3. Sections and Tiles.
- **Project Update**: Refactor the project layout using Bulma containers and columns.

**HTML Example**:
```html
<div class="container">
    <div class="columns">
        <div class="column is-one-third">
            <p>One-third column</p>
        </div>
        <div class="column is-two-thirds">
            <p>Two-thirds column</p>
        </div>
    </div>
</div>
```

### Navigations, Menus, and More
- **Objective**: Implementing navigation and other components.
- **Topics**:
  1. Bulma Navbar and Menu.
  2. Tabs, Breadcrumbs, and Pagination.
  3. Other Components (Card, Media Object).
- **Project Update**: Add a navbar and other components to the project.

**HTML Example**:
```html
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-menu">
        <a class="navbar-item" href="#">Home</a>
        <a class="navbar-item" href="#">About</a>
    </div>
</nav>

<div class="tabs">
    <ul>
        <li class="is-active"><a>Profile</a></li>
        <li><a>Jobs</a></li>
        <li><a>Contact</a></li>
    </ul>
</div>
```

### Form Elements and Buttons
- **Objective**: Use Bulma for styling forms and buttons.
- **Topics**:
  1. Styling Forms with Bulma (input, textarea, select).
  2. Bulma Buttons (sizes, colors, states).
  3. Control and Field Classes for Form Layout.
- **Project Update**: Redesign the form and buttons in the project using Bulma.

**HTML Example**:
```html
<div class="field">
    <label class="label">Name</label>
    <div class="control">
        <input class="input" type="text" placeholder="Text input">
    </div>

    <label class="label">Message</label>
    <div class="control">
        <textarea class="textarea" placeholder="Textarea"></textarea>
    </div>

    <div class="control">
        <button class="button is-primary">Submit</button>
    </div>
</div>
```

### Advanced Bulma Components
- **Objective**: Explore advanced Bulma components.
- **Topics**:
  1. Modal, Dropdown, and Tooltip.
  2. Message, Notification, and Progress Bars.
  3. Responsive and Interactive Elements.
- **Project Update**: Integrate advanced components into the project.

**HTML Example**:
```html
<button class="button is-primary modal-button" data-target="#myModal">Launch Modal</button>

<div id="myModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <p>Modal content...</p>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<article class="message">
    <div class="message-header">
        <p>Info</p>
        <button class="delete" aria-label="delete"></button>
    </div>
    <div class="message-body">
        Lorem ipsum dolor sit amet...
    </div>
</article>
```

### Customizing Bulma
- **Objective**: Learn how to customize Bulma.
- **Topics**:
  1. Overriding Bulma Variables.
  2. Building Bulma with Custom Styles.
  3. Best Practices in Customization.
- **Project Update**: Apply custom styles to Bulma components in the project.

**HTML Example**:
```html
<!-- Assume custom styles are applied here -->
<button class="button is-custom">Custom Styled Button</button>
```

### Final Project and Review
- **Objective**: Finalize the project and review all concepts.
- **Topics**:
  1. Recap of Bulma Components and Utilities.
  2. Best Practices with Bulma.
  3. Reviewing the Final Project.
- **Final Project**: Complete a comprehensive webpage using Bulma.

**HTML Example**:
```html
<!-- A combination of various Bulma components to create a complete webpage -->
<section class="hero is-info is-large">
    <div class="hero-body">
        <p class="title">
            Large Hero Title
        </p>
        <p class="subtitle">
            Hero Subtitle
        </p>
    </div>
</section>

<!-- Further sections with a combination of Bulma components -->
```
## Project Completion and Review
Each of these lessons and examples highlights the use of Bulma's classes and components. You are encouraged to experiment with these in the context of a webpage to see how Bulma simplifies layout and design tasks. Remember, for these examples to work, the Bulma CSS link needs to be included in the `<head>` of the HTML document.

**Deliverables**
Save the file as index.html