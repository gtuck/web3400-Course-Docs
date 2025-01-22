# Introduction to Bulma CSS Framework

We’ll follow a pattern similar to previous HTML and CSS sections to introduce the Bulma CSS Framework. To ensure consistency, a `starter.html` file is provided as a starting point. Each section focuses on specific aspects of Bulma, demonstrating its integration into web development projects. Bulma is a modern CSS framework based on Flexbox, offering a clean and user-friendly interface for styling web pages.

## Get the Index.html Starter Code

### Steps:

1. Open a VS Code terminal and navigate to your `assignments` folder:
   ```bash
   cd assignments
   ```
2. Create a new folder for Assignment 03:
   ```bash
   mkdir 03
   ```
3. Move into the newly created folder:
   ```bash
   cd 03
   ```
4. Download the `starter.html` file:
   ```bash
   wget "https://raw.githubusercontent.com/gtuck/web3400-Course-Docs/refs/heads/main/assignments/03/starter.html"
   ```
5. Create an `index.html` file from the starter file:
   ```bash
   cp starter.html index.html
   ```
6. Your `03` folder should now contain `starter.html` and `index.html`. You will only edit the `index.html` file.

### Add, Commit, and Push Changes:

1. Stage the new folder:
   ```bash
   git add 03
   ```
2. Commit the changes:
   ```bash
   git commit -m "Created assignment 03"
   ```
3. Push the changes:
   ```bash
   git push
   ```

## Getting Started with Bulma

### Objective:

Introduce Bulma and set it up in a project.

### Topics:

1. What is Bulma, and why use it?
2. Include Bulma, BulmaJS, and FontAwesome in your HTML (using CDN links).
3. Basic Bulma syntax and classes.

### Project:

Update the basic HTML page to include Bulma's CSS.

## Linking to Bulma CSS, BulmaJS, and FontAwesome

Add the following lines to the `<head>` section of your `index.html`:

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0/css/bulma.min.css">
<script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
```

These imports provide access to Bulma's styles, functionality for components, and icons.

## Styling Your `index.html`

### Steps:

We will complete the `index.html` file together in class.

## Conclusion

By following these steps, you’ll learn to apply Bulma styles to your HTML, making it more visually appealing and responsive. Bulma is highly customizable, so explore its documentation for advanced styling options.

## Stage, Commit, and Push the Final Changes

### Objective:

Commit and push your completed `index.html` file using a VS Code terminal.

### Steps:

1. Stage the changes:
   ```bash
   git add 03
   ```
2. Commit the changes:
   ```bash
   git commit -m "Update assignment 03"
   ```
3. Push the changes:
   ```bash
   git push
   ```
4. Confirm the updates on GitHub by visiting your forked repository.

## Submitting the Assignment

### Objective:

Submit the URL to your completed `index.html` file.

### Steps:

Submit the URL of your `03` folder in the format:
```
https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/03/
```
Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repository name.
