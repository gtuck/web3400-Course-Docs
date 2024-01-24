# Introduction to Bulma CSS Framework

We'll follow a pattern similar to the previous HTML and CSS sections to introduce the Bulma CSS Framework. However, to ensure we are all starting from the same place, I have provided a starter.html file for you to work from. Each section will focus on a specific aspect of Bulma, showing how to use its features in a project. This approach will help you understand how to integrate Bulma into your web development workflow. Bulma is a modern CSS framework based on Flexbox, offering a clean and easy-to-use interface for styling web pages. 

### Sync fork and update local repo.
- **Steps**:
  1. Navigate to your GitHub repository page for your fork of my web3400 repo.
  2. Run the "Sync fork" process to copy my `03/starter.html` folder/file into your GitHub repo assignments folder.
  3. Use GitHub Desktop (or the VSCode Terminal `git pull`) to 'pull' your repo, updating your local copy.
  4. In the same directory, `assignments/03,` make a copy of the starter.html file (Do not edit starter.html) and name it index.html (from the terminal `cp starter.html index.html`).

You should now have a `03` folder in your assignments directory that includes the `starter.html` and `index.html` files. We will only be making edits to the `index.html` file.

### Getting Started with Bulma
- **Objective**: Introduction to Bulma and setting it up in a project.
- **Topics**:
  1. What is Bulma, and why do you use it?
  2. Include Bulma, BulmaJS, and FontAwesome in your HTML (using CDN links/sources).
  3. Basic Bulma Syntax and Classes.
- **Project**: Update the basic HTML page to include Bulma's CSS.

### Linking to Bulma CSS, BulmaJS, and FontAwesome
Before styling the HTML elements, we must include the Bulma CSS, BulmaJS, and FontAwesome files in our HTML. Add the following lines in the `<head>` section of your `index.html` document:

```html
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.4/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0/dist/bulma.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
```

These lines import the Bulma CSS, BulmaJS, and FontAwesome from CDNs, making all the Bulma styles available for our use, adding functionality to some of the components, and the ability to use icons.

### Styling your index.html
- **Steps**:
  1. We will complete the index.html file together in class.

### Conclusion
By following these steps, you will better understand how to apply Bulma styles to your HTML structure, making it more visually appealing and responsive. Remember, Bulma is highly customizable, so feel free to explore its documentation for more advanced styling options.

## Stage, Commit, and Push the Final Changes
- **Objective**: Commit and push your completed index.html file in a VS Code Terminal.
- **Topics**:
  1. Stage the Change: Run `git add 03`.
  2. Commit the Changes: Type `git commit -m "Update assignment 03"`.
  3. Push the Changes: Run `git push`.
  4. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Assignment
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated `02` files in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/03/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.
