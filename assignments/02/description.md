# Assignment 02: Introduction to CSS

Purpose: Extend your HTML page (from Assignment 01) with external CSS to control typography, color, layout, and component styling while reinforcing clean repository workflow.

## Learning Objectives
You can:
1. Duplicate a prior assignment folder safely (create `assignments/02/` from `assignments/01/`).
2. Link an external stylesheet and (optionally) a script file with proper `<head>` metadata.
3. Apply CSS to style text, links, images, semantic layout regions, tables, forms, and media.
4. Use modern layout (Flexbox or CSS Grid) for main content + aside.
5. Implement basic responsive adjustments (viewport-aware).
6. Maintain incremental Git commits and submit the correct file URL.

## Prerequisites
- Repository `web3400-fall25` created from template (A00).
- A01 completed (`assignments/01/index.html` present and working).
- Dev Container or Codespace running.
- Instructor collaborator (`gtuck`) already invited.

---

## Step 0. Copy Assignment 01 to Assignment 02
From repository root (or first `cd assignments`):

```bash
cd assignments
cp -R 01 02   # -R (recursive) copies folder contents
```

Confirm:

```bash
ls 02
# Expect: index.html (and any media you used)
```

Add & commit the new folder:

```bash
git add assignments/02
git commit -m "A02: copy A01 to start CSS assignment"
git push
```

(Do NOT edit A01 in place. Keep A01 unchanged.)

---

## Step 1. Prepare Files in 02
Inside `assignments/02/`:

```bash
cd assignments/02
touch styles.css
# (Optional JS placeholder)
touch script.js
```

Open `index.html` and update the `<head>` so metadata comes first, then external resources:

```html
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Assignment 02 – CSS introduction and styling demo">
  <title>Assignment 02 – Your Name</title>
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script> <!-- optional -->
</head>
```

Commit:

```bash
git add assignments/02/index.html assignments/02/styles.css assignments/02/script.js
git commit -m "A02: add head metadata and external resource links"
```

---

## Step 2. Base Styles (Typography & Color)
In `styles.css` add a base reset + typography:

```css
/* Base / Reset (minimal) */
* {
  box-sizing: border-box;
}

html {
  font-size: 16px;
}

body {
  margin: 0;
  font-family: system-ui, Arial, sans-serif;
  line-height: 1.5;
  color: #222;
  background: #f9fafb;
}

/* Color palette (example) */
:root {
  --accent: #1f4b99;
  --accent-alt: #f57c00;
  --surface: #ffffff;
  --border: #d0d7de;
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: base reset, typography, color variables"
```

---

## Step 3. Structural & Navigation Styling
Add semantic region styling and navigation link states:

```css
header,
nav,
main,
aside,
footer {
  padding: 1rem;
}

header, footer {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
}

nav a {
  text-decoration: none;
  color: var(--accent);
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
}

nav a:hover,
nav a:focus {
  background: var(--accent);
  color: #fff;
}

nav a:active {
  background: var(--accent-alt);
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: structural regions and navigation styles"
```

---

## Step 4. Layout (Flexbox or Grid)
Wrap main content + aside (ensure your HTML has a container, or target existing structure):

```css
main {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem;
}

/* Primary content area (sections) */
main > section {
  flex: 3 1 60%;
  background: var(--surface);
  padding: 1rem;
  border: 1px solid var(--border);
  border-radius: 6px;
  margin-bottom: 1rem;
}

/* Aside */
aside {
  flex: 1 1 25%;
  background: var(--surface);
  padding: 1rem;
  border: 1px solid var(--border);
  border-radius: 6px;
  position: sticky;
  top: 1rem;
  height: fit-content;
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: flex layout for main content and aside"
```

---

## Step 5. Text, Lists, Links, Media
Refine readability and media responsiveness:

```css
h1, h2, h3 {
  line-height: 1.2;
  margin-top: 0;
}

p {
  margin: 0 0 1rem;
}

ul, ol {
  margin: 0 0 1rem 1.25rem;
  padding: 0;
}

figure {
  margin: 1rem 0;
  text-align: center;
}

figure img,
video {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
}

audio, video {
  display: block;
  margin: 0.5rem auto;
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: text, list, and media styling"
```

---

## Step 6. Table & Form Styling + Accessibility
```css
table {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
  font-size: 0.95rem;
}

th, td {
  border: 1px solid var(--border);
  padding: 0.5rem 0.75rem;
  text-align: left;
}

thead {
  background: #eef2f6;
}

tbody tr:nth-child(even) {
  background: #f4f6f8;
}

form {
  display: grid;
  gap: 0.75rem;
  max-width: 480px;
  margin: 1rem 0;
}

form label {
  font-weight: 600;
}

input, textarea, button {
  font: inherit;
  padding: 0.5rem 0.65rem;
  border: 1px solid var(--border);
  border-radius: 4px;
}

input:focus,
textarea:focus,
button:focus {
  outline: 2px solid var(--accent-alt);
  outline-offset: 1px;
}

button {
  background: var(--accent);
  color: #fff;
  border: none;
  cursor: pointer;
}

button:hover {
  background: var(--accent-alt);
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: table and form styling with focus states"
```

---

## Step 7. Responsive Adjustments
Add media query for narrow screens:

```css
@media (max-width: 800px) {
  main {
    flex-direction: column;
  }
  aside {
    position: static;
    width: 100%;
  }
}
```

Commit:

```bash
git add assignments/02/styles.css
git commit -m "A02: responsive breakpoint for narrow screens"
```

---

## Step 8. Final Review & Cleanup
Checklist:
- No inline `<style>` blocks (all in `styles.css`).
- No unused selectors.
- Valid HTML still loads (open in browser).
- All external assets referenced via correct relative paths.

Final commit & push:

```bash
git push origin main
```

---

## Step 9. Submission
Submit direct file URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/assignments/02/index.html
```

Open in private/incognito window to verify access (instructor collaborator must be accepted).

---

## Self-Checklist
- Folder: `assignments/02/`
- Files: `index.html`, `styles.css` (optional `script.js`)
- `<head>` includes: charset, viewport, description, title, linked stylesheet
- External stylesheet used (no inline style sprawl)
- Typography & color variables defined
- Layout uses Flexbox (or Grid) for main + aside
- Navigation styled with hover/focus
- Media responsive (images/video scale)
- Table styled (banding + header background)
- Form styled; labels associated; focus states visible
- Responsive breakpoint working (test by resizing)
- Incremental commit history present
- Submission URL correct

---

## Common Mistakes
- Editing `assignments/01` instead of working in `02`
- Forgetting `git add assignments/02` before first commit
- Leaving inline styles or `<font>` tags
- Missing `meta viewport` causing layout issues on mobile
- Copying HTML but breaking relative paths to assets
- Not pushing `styles.css` (file untracked)
- Putting CSS inside `index.html` instead of `styles.css`
- Using `cp 01 02` (without `-R`) leading to copy failure

---

## Rubric (30 pts)
- Complete/Incomplete

---

## Optional Enhancements (Not Required)
- CSS custom properties expanded for spacing scale
- Dark mode (prefers-color-scheme media query)
- Print stylesheet
- Smooth scroll for nav anchor links

---

## Support
Tag questions with [A02]. Include screenshot + specific issue. Use office hours for deeper feedback.

## Academic Integrity
Write your own CSS. Discussion of approaches OK; do not copy another student’s
