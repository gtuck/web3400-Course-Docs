# HTML

## Create an HTML file 
- **Objective**: Understand how to use VS Code to create a new HTML file and push updates to your web3400 GitHub repo.
- **Topics**:
  1. In your web3400 Docker Container: create a new folder named `01` in your `assignments` folder
  2. In your newly created `01` folder, create a new HTML file named `index.html`.
  3. Add, commit, and push the file to your repo.
     1. Stage the Change: Execute `git add index.html`.
     2. Commit the Change: Type `git commit -m "Created index.html"`.
     3. Push the Change: Run `git push`.
- **Project**: Create an empty HTML file in the correct folder.

## Introduction to HTML
- **Objective**: Understand HTML and its role in web development.
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
    <p>This is a paragraph on my first webpage.</p>
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
- **Objective**: Add hyperlinks and images to a webpage.
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
<hr>
<form>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name"><br>
    <label for="message">Message:</label>
    <textarea id="message" name="message"></textarea><br>
    <button type="submit">Submit</button>
</form>
```

## Embedded Audio and Video
- **Objective**: Explore additional HTML5 features and APIs.
- **Topics**:
  1. HTML5 Audio and Video Tags.
   
### Example
```html
<audio controls>
    <source src="https://www.w3schools.com/html/horse.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<hr>
<video controls>
    <source src="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>
```

## Project Completion and Review
At this point, you have a complete HTML page with all the elements and structures learned. The final code combines all the above snippets and is organized into a coherent webpage (index.html).

## Commit and Push the Final Changes
- **Objective**: Commit and push your completed index.html file in a VS Code Terminal.
- **Topics**:
  1. Commit the Change: Type `git commit -m "Update index.html"`.
  2. Push the Change: Run `git push`.
  3. Visit your web3400 repo on GitHub and verify your updates.
