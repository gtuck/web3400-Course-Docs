## Introduction to HTML

#### Create an HTML File

- **Objective**: Use VS Code to create a new HTML file and push updates to your `web3400-Spr2025` GitHub repository.
- **Steps**:
  1. Open the VS Code terminal and run:
     ```bash
     rm -r assignments/*
     ```
  2. In your `web3400` Docker Container:
     - Create a folder named `01` inside the `assignments` folder.
     - In the `01` folder, create a file named `index.html`.
  3. Add, commit, and push the file:
     - Navigate to the `01` folder:
       ```bash
       cd assignments/01
       ```
     - Stage the file:
       ```bash
       git add index.html
       ```
     - Commit the changes:
       ```bash
       git commit -m "Created index.html"
       ```
     - Push to GitHub:
       ```bash
       git push
       ```

- **Project**: Create an empty HTML file in the correct folder.

---

#### Introduction to HTML

- **Objective**: Understand HTML's role in web development.
- **Topics**:
  - History and Evolution of HTML.
  - Basic structure of an HTML document: `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>`.
  - Introduction to HTML tags and elements.
  - Using basic tags: `<h1>`, `<p>`, `<a>`.

- **Project**: Create a simple HTML page with a heading and a paragraph.

**Example**:
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

---

#### Exploring Text Formatting and Lists

- **Objective**: Learn to format text and create lists in HTML.
- **Topics**:
  - Text formatting tags: `<b>`, `<i>`, `<strong>`, `<em>`.
  - Creating ordered (`<ol>`) and unordered (`<ul>`) lists.
  - List items (`<li>`).

- **Project**: Add formatted text and both types of lists to your HTML page.

**Example**:
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

---

#### Understanding Hyperlinks and Images

- **Objective**: Add hyperlinks and images to your webpage.
- **Topics**:
  - Anchor tag `<a>`: attributes `href`, `target`.
  - Image tag `<img>`: attributes `src`, `alt`.
  - Relative vs absolute URLs.

- **Project**: Include hyperlinks and images in your HTML page.

**Example**:
```html
<a href="https://weber.edu" target="_blank">Visit weber.edu</a>
<hr>
<img width="320" src="https://source.unsplash.com/random/900x700/?fruit" alt="Descriptive text for the image">
```

---

#### Structuring Content with HTML5 Semantic Tags

- **Objective**: Use semantic tags for better webpage structure.
- **Topics**:
  - Semantic tags: `<header>`, `<footer>`, `<main>`, `<article>`, `<section>`, `<aside>`, `<nav>`.

- **Project**: Refactor your HTML page using semantic tags.

**Example**:
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
    <p>Copyright &copy; 2024</p>
</footer>
```

---

#### Tables and Forms

- **Objective**: Learn to create tables and simple forms.
- **Topics**:
  - Tables: `<table>`, `<tr>`, `<th>`, `<td>`.
  - Forms: `<form>`, `<input>`, `<label>`, `<textarea>`, `<button>`.
  - Form attributes: `action`, `method`.

- **Project**: Add a table and a form to your webpage.

**Example**:
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

---

#### Embedded Audio and Video

- **Objective**: Explore HTML5 features for multimedia.
- **Topics**:
  - Audio and video tags.

**Example**:
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

---

#### Commit and Push Final Changes

- **Objective**: Finalize your work and push it to GitHub.
- **Steps**:
  1. Commit your changes:
     ```bash
     git commit -m "Update index.html"
     ```
  2. Push to GitHub:
     ```bash
     git push
     ```
  3. Confirm changes by visiting your GitHub repository.

---

#### Submitting the Assignment

- **Objective**: Submit the URL to your completed HTML file.
- **Steps**:
  1. Submit the file URL in the format:
     ```
     https://github.com/[your-account-name]/[your-web3400-Spr2025-repo]/blob/main/assignments/01/index.html
     ```
     Replace `[your-account-name]` with your GitHub username and `[your-web3400-Spr2025-repo]` with your repository name.
