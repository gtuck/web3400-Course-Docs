# Assignment 03: Bulma CSS Framework Integration

Purpose: Rebuild / restyle the prior HTML content (from A01/A02) using the Bulma CSS framework to practice rapid UI prototyping, consistent styling, and responsive layout with minimal custom CSS.

## Why Use a CSS Framework?
Frameworks (Bulma, Bootstrap, Tailwind, etc.) provide:
- Speed: Predefined utility classes & components reduce boilerplate.
- Consistency: Shared spacing, colors, typography scale.
- Responsive layout: Grid/column systems and helpers out-of-the-box.
- Accessibility baselines: Many components start with reasonable defaults.
- Maintainability: Common vocabulary across team members.

Trade‑offs / When NOT to use:
- Overhead: Extra classes & unused CSS.
- Visual sameness if not customized.
- Learning curve for class names.
- Overrides: Can fight specificity when customizing deeply.

Bulma vs Others:
- Pure CSS (no JS dependency).
- Flexbox-first, semantic class naming.
- Lightweight compared to large multi-bundle frameworks.
- No inline utility explosion (vs Tailwind), yet more opinionated structure.

Use a framework when you need fast, consistent UI scaffolding. Drop down to custom CSS when you need unique branding or fine-grained control.

## Learning Objectives
You can:
1. Create `assignments/03/` with a Bulma-based `index.html`.
2. Include Bulma, (optional) BulmaJS, and Font Awesome via CDN.
3. Apply Bulma layout (container, columns) and at least five core components.
4. Style existing semantic content without rewriting structure meaninglessly.
5. Demonstrate responsive behavior (columns stack on narrow viewports).
6. Maintain incremental commits; submit correct file URL.

## Prerequisites
- Repo `web3400-fall25` (A00 complete).
- A01 & A02 present (for reference).
- Dev Container or Codespace running.
- Collaborator `gtuck` already invited.

---

## Step 0. Create Folder & Obtain Starter
From repository root:

```bash
cd assignments
mkdir 03
cd 03
# Download starter (choose ONE)
wget "https://raw.githubusercontent.com/gtuck/web3400-Course-Docs/main/assignments/03/starter.html" 2>/dev/null || \
curl -O "https://raw.githubusercontent.com/gtuck/web3400-Course-Docs/main/assignments/03/starter.html"
cp starter.html index.html
```

Add & initial commit:

```bash
git add assignments/03
git commit -m "A03: add Bulma starter"
git push
```

Only edit `index.html`. Keep `starter.html` as untouched reference.

---

## Step 1. Insert Bulma & Assets
In `index.html` `<head>` (order: meta → title → CSS → scripts):

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
<script src="https://cdn.jsdelivr.net/npm/@vizuaalog/bulmajs@0.12/dist/bulma.min.js" defer></script> <!-- optional -->
```

(Do not remove existing meta tags.)

Commit:

```bash
git add assignments/03/index.html
git commit -m "A03: link Bulma, Font Awesome, BulmaJS"
```

---

## Step 2. Transform Structure with Bulma Classes (Copilot use encouraged)
Apply BULMA CSS Classes to the HTML elements:
- Overall wrapper: `<section class="section"><div class="container">...</div></section>`
- Hero: `<section class="hero is-primary">`
- Navigation: `<nav class="navbar" role="navigation" aria-label="main navigation">`
- Columns layout: `<div class="columns"> <div class="column is-two-thirds"> ... </div> <div class="column"> ... </div> </div>`
- Cards for distinct content blocks: `<div class="card">...`
- Table: `<table class="table is-striped is-fullwidth">`
- Form controls: add `field`, `control`, `label`, `input.input`, `textarea.textarea`, `button.button.is-link`
- Buttons: `.button`, color modifiers (`is-primary`, `is-info`, etc.)
- Notifications or messages: `<div class="notification is-info">`

Commit after a logical chunk:

```bash
git add assignments/03/index.html
git commit -m "A03: apply hero, navbar, columns"
```

Repeat for additional components.

---

## Step 3. Add Icons (Font Awesome)
Embed icons inside buttons or headings:

```html
<button class="button is-link">
  <span class="icon"><i class="fas fa-paper-plane"></i></span>
  <span>Send</span>
</button>
```

Commit:

```bash
git add assignments/03/index.html
git commit -m "A03: add icons to buttons"
```

---

## Step 4. Ensure Responsive Behavior
Bulma columns auto-wrap. For emphasis you can specify breakpoints:

```html
<div class="column is-full-mobile is-half-tablet is-one-third-desktop">...</div>
```

Check in narrow width (VS Code responsive tools or resize browser). Adjust as needed.

Commit:

```bash
git add assignments/03/index.html
git commit -m "A03: refine responsive column classes"
```

---

## Step 5. Accessibility & Semantics Check
- Keep semantic tags (`header`, `main`, `footer`) where reasonable. Bulma classes attach to any element.
- Ensure buttons are `<button>` (not `<a>` unless navigation).
- Ensure alt text remains on images.

Commit:

```bash
git add assignments/03/index.html
git commit -m "A03: accessibility & semantic adjustments"
```

---

## Step 6. Final Review
Checklist (below). Then final push:

```bash
git push origin main
```

---

## Submission
Submit direct file URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/assignments/03/index.html
```

Open in a private/incognito window to verify visibility.

---

## Self-Checklist
- Folder: `assignments/03/`
- Files: `starter.html` (unchanged), `index.html` (modified)
- Bulma & Font Awesome linked (CDN)
- At least 5 Bulma components/utilities (e.g., hero, navbar, columns, card, table, buttons, form)
- Form uses Bulma form structure (`field`, `control`, etc.)
- Table styled (`table is-fullwidth is-striped` or similar)
- Responsive columns verified at mobile width
- Icons present (Font Awesome)
- Semantic HTML preserved (not replaced blindly by generic divs)
- Incremental commits (not a single huge commit)
- Correct submission URL

---

## Common Mistakes
- Editing `starter.html` instead of `index.html`
- Forgetting to commit folder before starting changes
- Omitting `<link>` to Bulma or placing scripts before meta charset
- Over-nesting columns (Bulma expects `.columns > .column`)
- Replacing semantic tags with `<div>` unnecessarily
- Using inline styles instead of Bulma classes
- Missing responsive test (layout breaks on narrow screens)
- Huge monolithic commit message (“finished A03” only)

---

## Rubric (30 pts)
- Complete/Incomplete

---

## Support
Tag questions with [A03]. Include screenshot + specific issue. Use office hours for deeper review.

## Academic Integrity
Write your own markup/class integration. Discuss approaches, but do not copy another student’s file
