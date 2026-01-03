### Mandatory Assignment 04 Status Review (5‑Minute Interview)

**Purpose**  
Quick verification that Assignment 04 (PHP templates) is correctly implemented before larger project work. This is a progress demo, not a troubleshooting session.

---

### Scheduling
Join via Office Hours Zoom Room: https://weber.zoom.us/j/8013088825  
Book a slot using the Google Appointment Schedule: https://calendar.app.google/AgmwbWiMHsVLH6yRA

If something breaks during the demo, move to the next item. For help, schedule a separate office hours slot.

---

### Student Preparation (Before Joining)
- Pull latest `main` locally (if working locally).
- Ensure `assignments/04/` loads in a browser (or forwarded container port).
- Have Git log visible or ready: `git log --oneline -n 5`
- Know where your `index.php` URL is on GitHub.

---

### Live Demo Flow (Target Timing)
1. (0:30) Open `assignments/04/` in editor / GitHub.
2. (1:00) Show folder + files (`index.php`, `templates/` with three files).
3. (1:30) Briefly open each template file (head, nav, footer).
4. (2:30) Show running page in browser (local dev server or Codespace).
5. (3:15) Show dynamic title usage.
6. (4:00) Answer conceptual questions.
7. (5:00) Outcome recorded.

---

### Verification Checklist

#### 1. Copy & Git Initialization
- [ ] `assignments/04/` created via `cp -R 03 04`
- [ ] Initial commit present (e.g., `"A04: copy A03 to start PHP templating assignment"`)

#### 2. Folder & File Structure
- [ ] `assignments/04/index.php` (no leftover `index.html`)
- [ ] `assignments/04/templates/head.php`
- [ ] `assignments/04/templates/nav.php`
- [ ] `assignments/04/templates/footer.php`  

#### 3. Template Content
- head.php:
  - [ ] DOCTYPE + opening `<html lang="en">`
  - [ ] `<head>` with meta charset, viewport, description, `<title>` using `<?= htmlspecialchars($pageTitle) ?>`
  - [ ] Bulma + Font Awesome & BulmaJS links (consistent versions)
  - [ ] Opens `<body>` (no closing tags here)
- nav.php:
  - [ ] `<nav>` markup only + (optional) hero section
  - [ ] No duplicate `<body>` / `<html>`
- footer.php:
  - [ ] `<footer>` markup
  - [ ] Closes `</body></html>`

#### 4. Page Assembly (index.php)
- [ ] Sets `$pageTitle` before including head
- [ ] Includes in order: head → nav → main content → footer
- [ ] Semantic structure (`<main>`, proper headings) intact

#### 5. Functionality
- [ ] Page loads without PHP warnings/errors
- [ ] Dynamic year and title renders
- [ ] Navigation links resolve (or gracefully 404 if extra pages not implemented)

#### 6. Git Hygiene
- [ ] Multiple logical commits (not a single “finished” commit)
- [ ] No unrelated changes in this assignment folder
- [ ] Pushed to `main`

#### 7. Submission URL (Matches A04 Instructions)
- [ ] Direct file URL format:
  ```
  https://github.com/<your-username>/web3400-spring26/blob/main/assignments/04/index.php
  ```

---

### Reference Commands (For Student Review)
```bash
# Create copy
cp -R assignments/03 assignments/04

# Create templates
mkdir assignments/04/templates
touch assignments/04/templates/{head.php,nav.php,footer.php}

# Remove old HTML
rm assignments/04/index.html

# Basic include test (inside index.php)
php -l assignments/04/index.php

# Run local server (from repo root)
php -S 0.0.0.0:8080
```

---

### Concept Questions (Be Ready)
1. Why split shared layout into includes?
2. How does `$pageTitle` improve maintainability?
3. What risk is reduced by centralizing `<head>` markup?
4. Explain how relative include paths resolve in PHP.
5. What would be your next abstraction step if pages grow to dozens?

---

### Common Issues (Quick Flags)
- Kept `index.html` (browser shows old file)
- Duplicate DOCTYPE in both head.php and index.php
- `<title>` hardcoded in multiple files
- Includes ordered incorrectly (footer before content)
- Missing `htmlspecialchars` on dynamic title
- Editing A03 instead of working in A04

---

### Outcome
- [ ] Approved (meets structural + functional criteria)
- [ ] Needs Revision (student will fix and reschedule / submit evidence)

Instructor Notes: (leave brief comment)

---

### After Interview (If Revisions Needed)
Student fixes → pushes changes → notifies via Canvas (WSU Online)/Email/Discussion (as instructed). No second live demo unless requested.
