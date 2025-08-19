# Assignment 01: Intro to HTML Fundamentals

Purpose: Build a single HTML page using core HTML elements while practicing repository organization and Git workflow consistent with Assignment 00.

## Learning Objectives
You can:
1. Create the required assignment folder and file (`assignments/01/index.html`).
2. Use valid HTML5 document structure with semantic elements.
3. Add headings, paragraphs, emphasis, links, images (with alt), lists, a table, a form, and embedded media.
4. Organize content with HTML5 semantic tags (`header`, `nav`, `main`, `section`, `article`, `aside`, `footer`).
5. Commit and push changes following a clear incremental workflow.
6. Submit the correct GitHub file URL.

## Prerequisites
- Repository `web3400-fall25` already created from the template (Assignment 00).
- Instructor (`gtuck`) added as collaborator.
- Dev Container or Codespace running (PHP not required for this assignment, but environment should already work).

---

## Step 1. Create Assignment Folder & File
Inside the repository root (NOT deleting anything):

```bash
mkdir -p assignments/01
code assignments/01/index.html
```

Do NOT remove other assignment folders. (Avoid destructive commands like `rm -r assignments/*`.)

---

## Step 2. Add Base HTML5 Structure
Paste this starter (adjust title and content as you go):

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assignment 01 – Your Name</title>
</head>
<body>
  <header>
    <h1>Assignment 01: Intro to HTML</h1>
    <nav>
      <a href="#about">About</a> |
      <a href="#lists">Lists</a> |
      <a href="#media">Media</a> |
      <a href="#form">Contact Form</a>
    </nav>
  </header>

  <main>
    <section id="about">
      <h2>About This Page</h2>
      <p>This page demonstrates fundamental HTML elements and semantic structure.</p>
      <p><strong>Goal:</strong> Practice clean markup and organization.</p>
    </section>

    <section id="text">
      <h2>Text & Emphasis</h2>
      <p>You can emphasize text with <em>em</em>, strengthen meaning with <strong>strong</strong>, or use <b>bold (visual)</b> and <i>italic (visual)</i>.</p>
    </section>

    <section id="lists">
      <h2>Lists</h2>
      <article>
        <h3>Ordered List</h3>
        <ol>
          <li>Plan content</li>
          <li>Write markup</li>
          <li>Validate and refine</li>
        </ol>
      </article>
      <article>
        <h3>Unordered List</h3>
        <ul>
          <li>Semantic tags</li>
          <li>Accessibility</li>
          <li>Clean structure</li>
        </ul>
      </article>
    </section>

    <section id="links-images">
      <h2>Links & Image</h2>
      <p>Visit the <a href="https://www.w3.org/" target="_blank" rel="noopener">W3C</a> for standards info.</p>
      <figure>
        <img src="https://source.unsplash.com/random/640x360/?web" alt="Random themed placeholder image">
        <figcaption>Example externally sourced placeholder image.</figcaption>
      </figure>
    </section>

    <section id="table">
      <h2>Sample Table</h2>
      <table>
        <thead>
          <tr>
            <th>Tag</th>
            <th>Purpose</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>&lt;header&gt;</td>
            <td>Introductory or navigational content</td>
          </tr>
          <tr>
            <td>&lt;main&gt;</td>
            <td>Primary unique content of the page</td>
          </tr>
        </tbody>
      </table>
    </section>

    <section id="media">
      <h2>Embedded Media</h2>
      <h3>Audio</h3>
      <audio controls>
        <source src="https://www.w3schools.com/html/horse.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>
      <h3>Video</h3>
      <video width="400" controls>
        <source src="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </section>

    <section id="form">
      <h2>Contact Form (Static)</h2>
      <form>
        <div>
          <label for="name">Name:</label>
          <input id="name" name="name" type="text" required>
        </div>
        <div>
          <label for="email">Email:</label>
          <input id="email" name="email" type="email" required>
        </div>
        <div>
          <label for="message">Message:</label><br>
          <textarea id="message" name="message" rows="4" cols="40"></textarea>
        </div>
        <button type="submit">Send</button>
      </form>
      <p><em>Note:</em> Form is not wired to a backend yet.</p>
    </section>

    <aside>
      <h2>Aside</h2>
      <p>Supplementary tips appear here.</p>
    </aside>
  </main>

  <footer>
    <p>&copy; 2025 Your Name. All rights reserved.</p>
  </footer>
</body>
</html>
```

---

## Step 3. Incremental Commits
Suggested commit flow (small, logical steps):

```bash
git add assignments/01/index.html
git commit -m "A01: base HTML skeleton"
# add lists
git add assignments/01/index.html
git commit -m "A01: add lists and text formatting"
# add media, table, form
git add assignments/01/index.html
git commit -m "A01: add table, media, and form"
git push origin main
```

You may combine steps, but smaller commits help feedback and rollback.

---

## Step 4. Validate & Review
Manual checks:
- HTML opens in browser (right-click → Open with Live Server if extension installed, or just open file).
- All elements properly nested.
- Image has meaningful alt text (update from placeholder if you change the image).

(Optional) Quick validation (online HTML validator) for structural warnings.

---

## Step 5. Submission
Submit this URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/assignments/01/index.html
```

Open it in a private/incognito window to ensure access (instructor must have collaborator rights).

---

## Self-Checklist
- Folder: `assignments/01/`
- File: `index.html`
- Valid `<!DOCTYPE html>` and `<html lang="en">`
- Semantic structure: header, nav, main, section(s), footer
- Includes: headings, paragraphs, emphasis, lists (ol + ul), link, image with alt, table, form, audio, video, aside
- Pushed to `main`
- Submission URL correct format

---

## Common Mistakes
- Wrong path (e.g., `assignment/01` vs `assignments/01`)
- Forgetting to add semantic tags (using only divs)
- No alt text on image
- One giant commit after all changes
- Using destructive shell commands that remove other assignments

---

## Rubric (20 pts)
- 2 pts: Correct location (`assignments/01/index.html`)
- 2 pts: Proper HTML5 boilerplate (doctype, lang, meta charset, viewport, title)
- 3 pts: Semantic layout (header, nav, main, at least two sections, footer) used appropriately
- 2 pts: Text content (headings hierarchy, paragraphs, emphasis elements used meaningfully)
- 2 pts: Lists (both ordered + unordered, valid structure)
- 2 pts: Link and image (image includes descriptive alt)
- 2 pts: Table (thead or header row + body rows)
- 2 pts: Form (labels associated via for/id; at least text, email, textarea, button)
- 2 pts: Media (audio + video embedded with fallback text)
- 1 pt: Aside present with relevant supplementary info
- 0 pts deduction avoidance: No destructive commands / repository damage
(Partial credit awarded if element present but incorrectly structured.)

---

## Optional Enhancements (Not Required)
- Add internal anchor links back to top.
- Use `<figure>` / `<figcaption>` for media consistently.
- Add basic inline styles (will cover CSS later—keep minimal).

---

## Support
Tag questions with [A01]. Provide screenshot + specific issue. Use office hours for deeper review.

## Academic Integrity
Original work required; you may reference docs. Do not copy another student
