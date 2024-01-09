# CSS

Basics of CSS alongside your HTML project to enhance the webpage. Here's how you can incorporate CSS into the existing HTML page (index.html):

## External CSS and Meta Styling
- **Objective**: Understand how to link external CSS files and style metadata elements.
- **CSS Example**:
  ```css
  /* This CSS would be placed in an external file like styles.css */

  body {
      background-color: #f0f0f0;
      color: #333;
  }
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
- **Integration with HTML Project**: Apply these styles to the HTML document created in the first session.

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
- **Integration with HTML Project**: Style the lists and formatted text in the HTML document from session 2.

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
- **Integration with HTML Project**: Apply these styles to the hyperlinks and images in the HTML document from session 3.

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
- **Integration with HTML Project**: Style the HTML5 semantic tags in the HTML document from session 4.

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
- **Integration with HTML Project**: Add styles to the table and form in the HTML document from session 5.

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
- **Integration with HTML Project**: Apply advanced CSS styles to finalize the project.

## Project Completion and Review
At this point, you have a complete HTML page with all the elements and structures learned. The final code combines all the above snippets and is organized into a coherent, well-structured and styled webpage.

**Deliverable**
Save the files as index.html and styles.css