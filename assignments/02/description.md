# CSS

Basics of CSS alongside your HTML project to enhance the webpage. Here's how you can incorporate CSS into the existing HTML page (index.html):

## External CSS and Meta Styling
- **Objective**: Understand how to link external CSS files and style metadata elements.
- **CSS Example**:
  ```html
    <head>
        <link rel="stylesheet" href="styles.css">
    </head>
  ```
- **Integration with HTML Project**: Link an external CSS file (e.g., `styles.css`) and apply these styles.

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
- **Objective**: Learn how to style tables and forms for better presentation.
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
At this point, you have a complete HTML page with all the elements and structures learned. The final code combines all the above snippets and is organized into a coherent, well-structured and styled webpage.

**Deliverable**
Save the files as index.html and styles.css