# Introduction to CSS (Assignment 02)

Basics of CSS alongside your HTML project (assignment 01) to enhance the webpage. Here's how you can incorporate CSS into the existing HTML page (index.html):

## Copy/Clone Assignment 01 to a new Assignment 02 folder. 
- **Objective**: Understand how to use the VS Code Terminal to `copy` the previous assignment folder and all of its files to a new assignment folder.
- **Topics**:
  1. In a VS Code Terminal, `cd` to your `assignments` folder.
  2. Recursively copy your folder: Run `cp -r 01 02`.
  4. Add, commit, and push the new `02` folder to your repo.
     2. Stage the Change: Execute `git add 02`.
     3. Commit the Change: Type `git commit -m "Created assignment 02"`.
     4. Push the Change: Run `git push`.

## Linking External Resources and Metadata
- **Objective**: Understand how to link external CSS files and use metadata.
- **Topics**:
  1. Linking CSS and JavaScript files.
  2. Using the `<link>` and `<script>` tags.
  3. Meta tags for character set, viewport, and description.
- **HTML Example**:
```html
<head>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A brief description of the page">
</head>
```
- **Integration with HTML Project (Assignment 01)**: Link an external CSS file (e.g., `styles.css`) and apply these styles.

## Introduction to CSS
- **Objective**: Understand the role of CSS in web design and learn basic syntax.
- **CSS Example**: 
  ```css
  body {
      font-family: Arial, sans-serif;
  }

  h1 {
      color: blue;
  }

  p {
      color: green;
  }
  ```

## Text Formatting and Lists with CSS
- **Objective**: Learn to style text and lists.
- **CSS Example**:
  ```css
  b, strong {
      font-weight: bold;
  }

  i, em {
      font-style: italic;
  }

  ol, ul {
      list-style-type: none; /* Removes default list styling */
      padding: 0;
  }

  li {
      margin-bottom: 5px;
  }
  ```

## Styling Hyperlinks and Images
- **Objective**: Understand how to style hyperlinks and images.
- **CSS Example**:
  ```css
  a {
      text-decoration: none;
      color: navy;
  }

  a:hover {
      color: red;
  }

  img {
      width: 100px;
      height: auto;
      border: 1px solid black;
  }
  ```

## Styling with HTML5 Semantic Tags
- **Objective**: Use CSS to enhance the layout and structure.
- **CSS Example**:
  ```css
  header, footer, nav, main, article, aside {
      border: 1px solid grey;
      margin: 10px;
      padding: 10px;
  }

  header, footer {
      background-color: lightgray;
  }

  nav {
      background-color: lightyellow;
  }
  ```

## Styling Tables and Forms
- **Objective**: Learn to style tables and forms for better presentation.
- **CSS Example**:
  ```css
  table {
      width: 100%;
      border-collapse: collapse;
  }

  th, td {
      border: 1px solid black;
      padding: 5px;
      text-align: left;
  }

  form {
      margin: 20px 0;
  }

  input, textarea, button {
      padding: 10px;
      margin: 5px 0;
  }
  ```

## Finalizing the Project with Advanced CSS
- **Objective**: Apply advanced CSS techniques to finalize the project.
- **CSS Example**:
  ```css
  /* Advanced styling like flexbox or grid layouts can be introduced here */

  main {
      display: flex;
      justify-content: space-between;
  }

  article {
      flex: 3;
  }

  aside {
      flex: 1;
  }
  ```

## Project Completion and Review
At this point, you have a complete HTML page with all the elements and structures learned. The final code combines all the above snippets and is organized into a coherent, well-structured, and styled webpage.

## Commit and Push the Final Changes
- **Objective**: Commit and push your completed index.html & styles.css files in a VS Code Terminal.
- **Topics**:
  1. Commit the Changes: Type `git commit -m "Update assignment 02"`.
  2. Push the Changes: Run `git push`.
  3. Confirm Changes on GitHub: Visit your forked repository on GitHub.

## Submitting the Assignment
- **Objective**: Submit the URL to your completed index.html file.
- **Topics**:
  1. Submit the URL of your updated `02` files in the format: `https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/02/`. Replace `[your-account-name]` with your GitHub username and `[your-web3400-repo]` with your repo name.
