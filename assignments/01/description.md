# HTML

## Introduction to HTML
- **Objective**: Understand what HTML is and its role in web development.
- **Topics**:
  1. History and Evolution of HTML.
  2. Basic Structure of an HTML Document (DOCTYPE, html, head, body).
  3. Introduction to Tags and Elements.
  4. Using Basic Tags: `<h1>`, `<p>`, `<a>`.
- **Project**: Create a simple HTML page with a heading and a paragraph.

### Example
```html
<!DOCTYPE html>
<html>
<head>
    <title>My First Webpage</title>
</head>
<body>
    <h1>Welcome to HTML!</h1>
    <p>This is a paragraph in my first webpage.</p>
</body>
</html>
```

## Exploring Text Formatting and Lists
- **Objective**: Learn how to format text and create lists in HTML.
- **Topics**:
  1. Text Formatting Tags: `<b>`, `<i>`, `<strong>`, `<em>`.
  2. Creating Ordered (`<ol>`) and Unordered (`<ul>`) Lists.
  3. List Items (`<li>`).
- **Project Update**: Add a section with formatted text and both types of lists to the HTML page.

### Example
```html
<b>Bold Text</b><br>
<i>Italic Text</i><br>
<strong>Strong Text</strong><br>
<em>Emphasized Text</em>

<ol>
    <li>First Item</li>
    <li>Second Item</li>
</ol>

<ul>
    <li>Bullet Item</li>
    <li>Another Bullet Item</li>
</ul>
```

## Understanding Hyperlinks and Images
- **Objective**: Learn to add hyperlinks and images to a webpage.
- **Topics**:
  1. Anchor Tag `<a>` and Attributes (href, target).
  2. Image Tag `<img>` and Attributes (src, alt).
  3. Relative vs Absolute URLs.
- **Project Update**: Include hyperlinks and images in the existing HTML page.

### Example
```html
<a href="https://weber.edu" target="_blank">Visit weber.edu</a>
<hr>
<img width="320" src="https://source.unsplash.com/random/900x700/?fruit" alt="Descriptive text for the image">
```

## Structuring Content with HTML5 Semantic Tags
- **Objective**: Understand and utilize HTML5 semantic tags for better structure.
- **Topics**:
  1. Header (`<header>`), Footer (`<footer>`), Main (`<main>`).
  2. Article (`<article>`), Section (`<section>`), Aside (`<aside>`).
  3. Navigation (`<nav>`).
- **Project Update**: Refactor the HTML page to include these semantic tags.

### Example
```html
<header>
    <h1>My Website Header</h1>
</header>

<nav>
    <a href="#">Home</a> | <a href="#">About</a>
</nav>

<main>
    <article>
        <h2>Article Title</h2>
        <p>Article content...</p>
    </article>
    <aside>
        <h2>Related Links</h2>
        <a href="#">Link 1</a>
    </aside>
</main>

<footer>
    <p>Copyright © 2024</p>
</footer>
```

## Tables and Forms
- **Objective**: Learn how to create tables and simple forms.
- **Topics**:
  1. Creating Tables: `<table>`, `<tr>`, `<th>`, `<td>`.
  2. Basic Forms: `<form>`, `<input>`, `<label>`, `<textarea>`, `<button>`.
  3. Form Attributes: action, method.
- **Project Update**: Add a table and a simple form to the webpage.

### Example
```html
<table>
    <tr>
        <th>Header 1</th>
        <th>Header 2</th>
    </tr>
    <tr>
        <td>Data 1</td>
        <td>Data 2</td>
    </tr>
</table>

<form>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name"><br>
    <label for="message">Message:</label>
    <textarea id="message" name="message"></textarea><br>
    <button type="submit">Submit</button>
</form>
```

## Linking External Resources and Metadata
- **Objective**: Understand how to link external resources and use metadata.
- **Topics**:
  1. Linking CSS and JavaScript files.
  2. Using the `<link>` and `<script>` tags.
  3. Meta tags for character set, viewport, and description.
- **Project Update**: Link a dummy CSS and JavaScript file; add meta tags.

### Example
```html
<head>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A brief description of the page">
</head>
```

## Embeded Video and Audio
- **Objective**: Explore additional HTML5 features and APIs.
- **Topics**:
  1. HTML5 Video and Audio Tags.
   
### Example
```html
<video controls>
    <source src="movie.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<audio controls>
    <source src="audio.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
```

## Project Completion and Review
At this point, you have a complete HTML page with all the elements and structures learned. The final code is a combination of all the above snippets, organized into a coherent webpage.

**Deliverable**
Save the file as index.html
