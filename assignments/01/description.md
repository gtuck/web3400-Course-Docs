# Assignment 01: Intro to HTML Fundamentals

Purpose: Build a single HTML page using core HTML elements while practicing repository organization and Git workflow consistent with Assignment 00.

## Learning Objectives
By completing this assignment, you will be able to:
1. **Create and organize files** - Set up the correct folder structure (`assignments/01/index.html`) without disrupting existing work.
2. **Write valid HTML5** - Construct a well-formed HTML document that passes validation and uses proper DOCTYPE and language attributes.
3. **Apply semantic HTML** - Choose appropriate semantic elements (`header`, `nav`, `main`, `section`, `article`, `aside`, `footer`) based on content meaning and structure.
4. **Implement core HTML elements** - Correctly use headings (h1-h6), paragraphs, emphasis, links, images with descriptive alt text, ordered/unordered lists, tables with headers, forms with labels, and multimedia elements.
5. **Follow accessibility guidelines** - Write HTML that supports screen readers and keyboard navigation through proper labeling, semantic structure, and meaningful content.
6. **Use version control effectively** - Make logical, incremental commits with clear messages that document your development process.
7. **Validate and test your work** - Use browser developer tools and validation services to ensure your HTML works correctly across different environments.

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

## Step 2. Build HTML Structure Progressively

Rather than copying a large block of code, you'll build your HTML step by step to understand each component. Start with the basic document structure:

### Step 2a. Document Foundation
Create the basic HTML5 document structure:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="HTML fundamentals demonstration page">
  <title>Assignment 01 – Your Name</title>
</head>
<body>
  <!-- Content will go here -->
</body>
</html>
```

**📝 Checkpoint:** Save and open in browser. You should see a blank page with your title in the browser tab.

### Step 2b. Semantic Page Structure
Add the main semantic containers:

```html
<body>
  <header>
    <h1>Assignment 01: HTML Fundamentals</h1>
    <nav>
      <!-- Navigation will go here -->
    </nav>
  </header>

  <main>
    <!-- Main content will go here -->
  </main>

  <footer>
    <!-- Footer content will go here -->
  </footer>
</body>
```

**📝 Checkpoint:** Refresh browser. You should see your main heading.

### Step 2c. Navigation
Add accessible navigation with proper linking:

```html
<nav aria-label="Page sections">
  <ul>
    <li><a href="#about">About</a></li>
    <li><a href="#text">Text Elements</a></li>
    <li><a href="#lists">Lists</a></li>
    <li><a href="#media">Media</a></li>
    <li><a href="#data">Data Tables</a></li>
    <li><a href="#forms">Forms</a></li>
  </ul>
</nav>
```

**💡 Why:** Using `<ul>` for navigation and `aria-label` improves accessibility for screen readers.

### Step 2d. Main Content Sections
Add the content sections one by one. Start with the about section:

```html
<main>
  <section id="about">
    <h2>About This Page</h2>
    <p>This page demonstrates fundamental HTML elements and semantic structure following modern web standards.</p>
    <p><strong>Learning Goal:</strong> Practice clean, accessible markup and proper document organization.</p>
  </section>
</main>
```

**📝 Checkpoint:** Test navigation - clicking "About" should scroll to this section.

Continue adding each section individually in the following steps.

### Step 2e. Text Elements and Emphasis
Add a section demonstrating different text elements:

```html
<section id="text">
  <h2>Text Elements and Emphasis</h2>
  <p>HTML provides several ways to add meaning and emphasis to text:</p>
  <ul>
    <li><strong>Important content</strong> uses the strong element</li>
    <li><em>Emphasized content</em> uses the em element</li>
    <li><mark>Highlighted text</mark> uses the mark element</li>
    <li><code>Code snippets</code> use the code element</li>
    <li><abbr title="HyperText Markup Language">HTML</abbr> abbreviations should be explained</li>
  </ul>
  <blockquote cite="https://www.w3.org/WAI/intro/accessibility">
    <p>"The power of the Web is in its universality. Access by everyone regardless of disability is an essential aspect."</p>
    <footer>— <cite>Tim Berners-Lee, W3C Director and inventor of the World Wide Web</cite></footer>
  </blockquote>
</section>
```

**💡 Why:** Using semantic elements like `<strong>`, `<em>`, `<abbr>`, and `<cite>` provides meaning, not just visual styling.

### Step 2f. Lists with Proper Structure
Add both ordered and unordered lists:

```html
<section id="lists">
  <h2>Lists</h2>
  <article>
    <h3>Steps for Good HTML (Ordered List)</h3>
    <ol>
      <li>Plan your content structure first</li>
      <li>Choose semantic elements based on meaning</li>
      <li>Write valid, accessible markup</li>
      <li>Test in multiple browsers and with keyboard navigation</li>
      <li>Validate your HTML</li>
    </ol>
  </article>
  
  <article>
    <h3>Key HTML Principles (Unordered List)</h3>
    <ul>
      <li>Semantic markup over visual styling</li>
      <li>Accessibility for all users</li>
      <li>Progressive enhancement</li>
      <li>Clean, maintainable code</li>
      <li>Web standards compliance</li>
    </ul>
  </article>

  <article>
    <h3>HTML5 Semantic Elements (Description List)</h3>
    <dl>
      <dt>&lt;header&gt;</dt>
      <dd>Contains introductory content or navigation aids</dd>
      <dt>&lt;main&gt;</dt>
      <dd>Represents the dominant content of the document</dd>
      <dt>&lt;article&gt;</dt>
      <dd>Self-contained composition that could be distributed independently</dd>
      <dt>&lt;section&gt;</dt>
      <dd>Generic section of content, typically with a heading</dd>
    </dl>
  </article>
</section>
```

**💡 Why:** Description lists (`<dl>`, `<dt>`, `<dd>`) are perfect for term-definition pairs.

### Step 2g. Media with Accessibility
Add images and multimedia with proper accessibility:

```html
<section id="media">
  <h2>Images and Media</h2>
  
  <article>
    <h3>Images with Proper Alt Text</h3>
    <p>Visit the <a href="https://www.w3.org/" target="_blank" rel="noopener noreferrer">W3C website</a> for web standards information.</p>
    <figure>
      <img src="https://picsum.photos/640/360?random=1" 
           alt="Placeholder image demonstrating proper alt text usage">
      <figcaption>Example image with descriptive alt text and caption.</figcaption>
    </figure>
  </article>

  <article>
    <h3>Audio Content</h3>
    <p>Audio elements should include controls and fallback content:</p>
    <audio controls aria-label="Example audio demonstrating HTML audio element">
      <source src="https://www.soundjay.com/misc/sounds/bell-ringing-05.wav" type="audio/wav">
      <source src="https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3" type="audio/mpeg">
      <p>Your browser doesn't support HTML audio. <a href="https://www.soundjay.com/misc/sounds/bell-ringing-05.mp3">Download the audio file</a>.</p>
    </audio>
  </article>

  <article>
    <h3>Video Content</h3>
    <p>Videos should be accessible and include captions when possible:</p>
    <video controls width="400" aria-label="Example video demonstrating HTML video element">
      <source src="https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4" type="video/mp4">
      <source src="https://sample-videos.com/zip/10/webm/SampleVideo_1280x720_1mb.webm" type="video/webm">
      <p>Your browser doesn't support HTML video. <a href="https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4">Download the video file</a>.</p>
    </video>
  </article>
</section>
```

**💡 Why:** Multiple source formats ensure compatibility, and fallback content helps when media fails to load.

### Step 2h. Data Tables with Accessibility
Create a properly structured data table:

```html
<section id="data">
  <h2>Data Tables</h2>
  <p>Tables should only be used for tabular data, not layout. Here's a comparison of HTML elements:</p>
  
  <table>
    <caption>HTML5 Semantic Elements Comparison</caption>
    <thead>
      <tr>
        <th scope="col">Element</th>
        <th scope="col">Purpose</th>
        <th scope="col">Can Contain</th>
        <th scope="col">Common Use</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row">&lt;header&gt;</th>
        <td>Introductory content</td>
        <td>Headings, nav, paragraphs</td>
        <td>Page/section headers</td>
      </tr>
      <tr>
        <th scope="row">&lt;nav&gt;</th>
        <td>Navigation links</td>
        <td>Lists of links</td>
        <td>Main navigation, breadcrumbs</td>
      </tr>
      <tr>
        <th scope="row">&lt;main&gt;</th>
        <td>Primary content</td>
        <td>Articles, sections</td>
        <td>Main page content area</td>
      </tr>
      <tr>
        <th scope="row">&lt;article&gt;</th>
        <td>Self-contained content</td>
        <td>Headings, paragraphs, media</td>
        <td>Blog posts, news articles</td>
      </tr>
      <tr>
        <th scope="row">&lt;section&gt;</th>
        <td>Thematic grouping</td>
        <td>Headings, any content</td>
        <td>Content sections</td>
      </tr>
      <tr>
        <th scope="row">&lt;aside&gt;</th>
        <td>Tangential content</td>
        <td>Various content types</td>
        <td>Sidebars, related links</td>
      </tr>
      <tr>
        <th scope="row">&lt;footer&gt;</th>
        <td>Closing content</td>
        <td>Copyright, links, contact</td>
        <td>Page/section footers</td>
      </tr>
    </tbody>
  </table>
</section>
```

**💡 Why:** `<caption>`, `<th scope="col/row">`, and proper table structure help screen readers navigate data.

### Step 2i. Accessible Forms
Create a form with proper labeling and structure:

```html
<section id="forms">
  <h2>Forms and Input Elements</h2>
  <p>Forms should be accessible and provide clear feedback to users:</p>
  
  <form action="#" method="post" novalidate>
    <fieldset>
      <legend>Contact Information</legend>
      
      <div class="form-group">
        <label for="fullName">Full Name (required)</label>
        <input type="text" id="fullName" name="fullName" required 
               aria-describedby="fullName-help">
        <small id="fullName-help">Enter your first and last name</small>
      </div>

      <div class="form-group">
        <label for="email">Email Address (required)</label>
        <input type="email" id="email" name="email" required 
               aria-describedby="email-help">
        <small id="email-help">We'll never share your email with anyone</small>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" 
               aria-describedby="phone-help">
        <small id="phone-help">Optional: Include area code</small>
      </div>
    </fieldset>

    <fieldset>
      <legend>Message Details</legend>
      
      <div class="form-group">
        <label for="subject">Subject</label>
        <select id="subject" name="subject">
          <option value="">Choose a topic...</option>
          <option value="general">General Question</option>
          <option value="technical">Technical Support</option>
          <option value="feedback">Feedback</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" 
                  aria-describedby="message-help"></textarea>
        <small id="message-help">Please provide details about your inquiry</small>
      </div>

      <div class="form-group">
        <input type="checkbox" id="newsletter" name="newsletter" value="yes">
        <label for="newsletter">Subscribe to our newsletter</label>
      </div>
    </fieldset>

    <div class="form-actions">
      <button type="submit">Send Message</button>
      <button type="reset">Clear Form</button>
    </div>
  </form>
  
  <p><strong>Note:</strong> This is a demonstration form - it won't actually send data anywhere.</p>
</section>
```

**💡 Why:** `<fieldset>`, `<legend>`, proper labeling, and `aria-describedby` make forms accessible to all users.

### Step 2j. Aside and Footer
Complete your page with supplementary content and footer:

```html
<aside>
  <h2>Additional Resources</h2>
  <h3>Web Development Resources</h3>
  <ul>
    <li><a href="https://developer.mozilla.org/en-US/docs/Web/HTML" target="_blank" rel="noopener noreferrer">MDN HTML Documentation</a></li>
    <li><a href="https://www.w3.org/WAI/WCAG21/quickref/" target="_blank" rel="noopener noreferrer">WCAG 2.1 Quick Reference</a></li>
    <li><a href="https://validator.w3.org/" target="_blank" rel="noopener noreferrer">W3C Markup Validator</a></li>
    <li><a href="https://wave.webaim.org/" target="_blank" rel="noopener noreferrer">WAVE Accessibility Checker</a></li>
  </ul>
  
  <h3>HTML Best Practices</h3>
  <ol>
    <li>Always use semantic HTML elements</li>
    <li>Provide alternative text for images</li>
    <li>Use proper heading hierarchy</li>
    <li>Ensure keyboard accessibility</li>
    <li>Validate your markup regularly</li>
  </ol>
</aside>
```

And in your footer:

```html
<footer>
  <p>&copy; 2025 <strong>Your Name</strong>. Created for Web Development Fundamentals.</p>
  <p><small>This page demonstrates HTML5 semantic elements and accessibility best practices.</small></p>
</footer>
```

**📝 Final Checkpoint:** Your complete page should now have all required elements and pass accessibility checks.

---

## Step 3. Incremental Development and Git Workflow

Commit your work at logical checkpoints to maintain a clear development history. Here's the recommended workflow:

### Initial Setup Commit
```bash
git add assignments/01/index.html
git commit -m "A01: Create basic HTML5 document structure"
git push origin main
```

### Progressive Development Commits
After completing each section from Step 2:

```bash
# After Step 2d (about section)
git add assignments/01/index.html
git commit -m "A01: Add semantic structure and about section"

# After Step 2e (text elements)
git add assignments/01/index.html
git commit -m "A01: Add text elements and emphasis examples"

# After Step 2f (lists)
git add assignments/01/index.html
git commit -m "A01: Add ordered, unordered, and description lists"

# After Step 2g (media)
git add assignments/01/index.html
git commit -m "A01: Add accessible images, audio, and video"

# After Step 2h (tables)
git add assignments/01/index.html
git commit -m "A01: Add data table with proper structure"

# After Step 2i (forms)
git add assignments/01/index.html
git commit -m "A01: Add accessible contact form"

# After Step 2j (aside and footer)
git add assignments/01/index.html
git commit -m "A01: Complete page with aside and footer"

# Final push
git push origin main
```

### 💡 Git Best Practices
- **Commit early and often** - Each section is a logical unit of work
- **Write descriptive messages** - Explain what you added, not just that you changed something
- **Test before committing** - Make sure your HTML validates and displays correctly
- **Push regularly** - Don't wait until the end to share your work

### 🔍 Verify Your Commits
Check your commit history:
```bash
git log --oneline -10
```
You should see multiple commits showing your progression through the assignment.

---

## Step 4. Validation, Testing, and Quality Assurance

Proper testing ensures your HTML works for all users and meets web standards.

### 4a. Browser Testing
1. **Open in multiple browsers** (Chrome, Firefox, Safari, Edge if available)
2. **Test responsive behavior** - Resize your browser window to check different screen sizes
3. **Test keyboard navigation** - Use Tab key to navigate through all interactive elements
4. **Check with browser developer tools**:
   - Right-click → Inspect Element
   - Look for any console errors (red messages)
   - Use the Accessibility tab to check for issues

### 4b. HTML Validation
Validate your markup to catch errors:

1. **W3C Markup Validator**: https://validator.w3.org/
   - Either upload your file or paste your HTML
   - Fix any errors (warnings are okay for this assignment)
   - Your HTML should show "Document checking completed. No errors or warnings to show."

2. **Quick Manual Checks**:
   - All opening tags have matching closing tags
   - All images have meaningful alt text (not just "image" or "placeholder")
   - All form inputs have associated labels
   - Links open in new tabs where appropriate

### 4c. Accessibility Testing
Ensure your page works for users with disabilities:

1. **WAVE Web Accessibility Evaluator**: https://wave.webaim.org/
   - Enter your GitHub Pages URL or copy/paste your HTML
   - Address any errors (warnings and features are informational)

2. **Manual Accessibility Checks**:
   - Turn off CSS/images in browser - content should still make sense
   - Use a screen reader (VoiceOver on Mac, NVDA on Windows)
   - Navigate entire page using only Tab, Enter, and Arrow keys
   - Ensure all images have descriptive alt text
   - Check that form labels are properly associated

### 4d. Content Quality Check
Review your content for:
- **Spelling and grammar** - Use browser spell-check or Grammarly
- **Meaningful content** - Replace placeholder text with your own words
- **Logical structure** - Content flows logically from section to section
- **Proper heading hierarchy** - h1 → h2 → h3 (no skipping levels)

### 🚨 Common Issues to Fix
- **Missing alt text**: Every `<img>` needs meaningful alt text
- **Broken links**: Test all external links open correctly
- **Invalid HTML**: Check for unclosed tags or typos in element names
- **Poor accessibility**: Ensure all interactive elements are keyboard accessible
- **Mixed content**: All resources should use HTTPS (not HTTP)

### ✅ Quality Checklist
Before submitting, verify:
- [ ] Page loads without errors in browser
- [ ] All sections are accessible via navigation
- [ ] Images display with appropriate alt text
- [ ] Forms are properly labeled and functional
- [ ] No HTML validation errors
- [ ] No accessibility errors in WAVE
- [ ] Content is your own (not just placeholder text)
- [ ] All external links work and open appropriately

---

## Step 5. Submission
Submit this URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/assignments/01/index.html
```

Open it in a private/incognito window to ensure access (instructor must have collaborator rights).

---

## Self-Assessment Checklist

Use this comprehensive checklist before submission:

### 📁 File Organization (Required)
- [ ] Folder: `assignments/01/` (exact path)
- [ ] File: `index.html` (exact filename)
- [ ] No extra or unnecessary files in the folder
- [ ] File is committed and pushed to `main` branch

### 🏗️ HTML Structure (Required)
- [ ] Valid `<!DOCTYPE html>` declaration
- [ ] `<html lang="en">` attribute set
- [ ] Complete `<head>` section with charset, viewport, description, and title
- [ ] Semantic structure: `<header>`, `<nav>`, `<main>`, `<section>`, `<aside>`, `<footer>`
- [ ] Proper heading hierarchy (h1 → h2 → h3, no skipped levels)

### 📝 Content Elements (Required)
- [ ] Multiple paragraphs with meaningful content
- [ ] Text emphasis using `<strong>`, `<em>`, `<mark>`, `<code>`
- [ ] At least one external link with proper `target` and `rel` attributes
- [ ] At least one image with descriptive alt text (not generic)
- [ ] Ordered list (`<ol>`) with meaningful content
- [ ] Unordered list (`<ul>`) with meaningful content
- [ ] Description list (`<dl>`) with term-definition pairs
- [ ] Data table with `<caption>`, `<thead>`, `<tbody>`, and proper scope attributes
- [ ] Contact form with proper `<fieldset>`, `<legend>`, and labeled inputs
- [ ] Audio element with multiple sources and fallback content
- [ ] Video element with multiple sources and fallback content
- [ ] Aside with supplementary content

### ♿ Accessibility (Required)
- [ ] All images have meaningful alt text
- [ ] All form inputs have associated labels
- [ ] Navigation uses proper list structure
- [ ] Table headers use appropriate scope attributes
- [ ] Form groups use fieldset and legend appropriately
- [ ] Links indicate when they open in new windows
- [ ] Page passes WAVE accessibility check with no errors

### 🔧 Technical Quality (Required)
- [ ] HTML validates with no errors at validator.w3.org
- [ ] All external links work correctly
- [ ] Page displays correctly in browser
- [ ] All interactive elements work as expected
- [ ] No broken images or media
- [ ] Proper file paths and URLs

### 📚 Development Process (Required)
- [ ] Multiple logical Git commits (not just one big commit)
- [ ] Commit messages are clear and descriptive
- [ ] Code is properly indented and formatted
- [ ] Content is original work (not copied from examples)

### 🌐 Submission (Required)
- [ ] Correct GitHub file URL format submitted
- [ ] Repository collaborator access granted to instructor
- [ ] File accessible in private/incognito browser window

---

## Common Issues and How to Avoid Them

Learning from common mistakes helps you submit higher-quality work. Here are the most frequent issues and prevention strategies:

### 📁 File and Folder Issues
**Problem**: Wrong folder path (e.g., `assignment/01` vs `assignments/01`)
- **Prevention**: Double-check the exact path requirements: `assignments/01/index.html`
- **Check**: Use `pwd` in terminal to verify your current directory

**Problem**: Committing work to wrong assignment folder
- **Prevention**: Always use `git status` before committing to see which files you're adding
- **Fix**: If you edited the wrong folder, copy your changes to the correct location

### 🏗️ HTML Structure Issues
**Problem**: Using only `<div>` elements instead of semantic tags
- **Prevention**: Ask "What does this content mean?" not "How should it look?"
- **Examples**: Use `<header>` for page headers, `<nav>` for navigation, `<main>` for primary content

**Problem**: Missing or generic alt text on images
- **Prevention**: Describe what the image shows and its purpose, not just "image" or "placeholder"
- **Good example**: `alt="Students collaborating on a web development project in a classroom"`
- **Bad example**: `alt="image"` or `alt="placeholder"`

**Problem**: Improper heading hierarchy (h1 → h3, skipping h2)
- **Prevention**: Think of headings as an outline - each level should logically follow the previous
- **Check**: Your page outline should make sense when read as a table of contents

### ♿ Accessibility Problems
**Problem**: Form inputs without proper labels
- **Prevention**: Every `<input>`, `<textarea>`, and `<select>` needs an associated `<label>`
- **Correct**: `<label for="email">Email</label><input id="email" type="email">`

**Problem**: Tables used for layout instead of data
- **Prevention**: Only use `<table>` for actual tabular data (like spreadsheet data)
- **Alternative**: Use CSS Grid or Flexbox for layout instead

**Problem**: Links that don't indicate they open in new windows
- **Prevention**: Use `target="_blank" rel="noopener noreferrer"` and consider adding visual or text indicators

### 🔧 Technical Issues
**Problem**: Mixed HTTP/HTTPS content causing security warnings
- **Prevention**: Use HTTPS URLs for all external resources (images, videos, etc.)
- **Check**: Look for `http://` in your code and change to `https://`

**Problem**: Broken external links or media
- **Prevention**: Test all links before submitting
- **Check**: Click every link and verify media loads properly

**Problem**: HTML validation errors
- **Prevention**: Use the W3C validator regularly during development, not just at the end
- **Common errors**: Unclosed tags, misspelled attributes, invalid nesting

### 📚 Development Process Issues
**Problem**: One giant commit after all changes ("finished assignment")
- **Prevention**: Commit after completing each section or major component
- **Better practice**: Make 5-8 commits showing your progression through the assignment

**Problem**: Using destructive commands that remove other work
- **Prevention**: Avoid commands like `rm -rf` or `git reset --hard` unless you understand exactly what they do
- **Safe practice**: Use `git status` and `git diff` to check changes before committing

### 🔍 Self-Check Before Submitting
Run through this quick checklist:
1. **Open your HTML file in a browser** - Does everything display correctly?
2. **Check the W3C validator** - Are there any validation errors?
3. **Test keyboard navigation** - Can you tab through all interactive elements?
4. **Verify your GitHub URL** - Does the link you're submitting actually work in an incognito window?
5. **Review your commit history** - Do you have multiple logical commits?

### 🆘 Getting Help
If you encounter these issues:
- **Check the discussion forum first** - Someone may have had the same problem
- **Tag questions with [A01]** and include screenshots
- **Use office hours** for hands-on troubleshooting
- **Don't wait until the last minute** - Technical issues always take longer than expected

---

## Rubric (30 points total)

This assignment uses a detailed rubric to provide meaningful feedback on your HTML fundamentals.

### 🏗️ HTML Structure & Semantics (10 points)
- **Excellent (9-10 pts)**: Perfect semantic structure with all required elements correctly used, valid HTML5 document, proper heading hierarchy
- **Good (7-8 pts)**: Mostly correct semantic structure, minor validation issues or semantic choices, generally good organization
- **Satisfactory (5-6 pts)**: Basic structure present but some semantic elements misused or missing, some validation errors
- **Needs Improvement (0-4 pts)**: Major structural issues, significant validation errors, poor or missing semantic markup

### ♿ Accessibility & Best Practices (8 points)
- **Excellent (7-8 pts)**: All images have meaningful alt text, forms properly labeled, excellent accessibility practices, passes WAVE with no errors
- **Good (6 pts)**: Minor accessibility issues, mostly proper labeling, passes WAVE with warnings only
- **Satisfactory (4-5 pts)**: Some accessibility features present but inconsistent, some WAVE errors
- **Needs Improvement (0-3 pts)**: Poor or missing accessibility features, significant WAVE errors, unusable for screen readers

### 📝 Content Quality & Completeness (7 points)
- **Excellent (6-7 pts)**: All required elements present with meaningful, original content, well-written and engaging
- **Good (5 pts)**: Most elements present with good content, minor missing components
- **Satisfactory (3-4 pts)**: Basic requirements met but content is generic or minimal
- **Needs Improvement (0-2 pts)**: Missing major components, placeholder content not replaced, very minimal effort

### 🔧 Technical Implementation & Process (5 points)
- **Excellent (5 pts)**: Clean, well-formatted code, multiple logical commits, proper Git workflow, no technical issues
- **Good (4 pts)**: Generally clean code, good commit history, minor technical issues
- **Satisfactory (2-3 pts)**: Code works but formatting issues, few commits or poor commit messages
- **Needs Improvement (0-1 pts)**: Major technical issues, single commit or no commit history, broken functionality

### 📊 Grade Breakdown
- **27-30 points**: A (90-100%) - Exceptional work demonstrating mastery of HTML fundamentals
- **24-26 points**: B (80-89%) - Good work with minor areas for improvement
- **21-23 points**: C (70-79%) - Satisfactory work meeting basic requirements
- **18-20 points**: D (60-69%) - Below expectations, major revisions needed
- **0-17 points**: F (Below 60%) - Does not meet assignment requirements

### 🔄 Revision Policy
If you score below 24 points (B-level), you may revise and resubmit your assignment within one week for up to 80% of the original points available. Contact the instructor for specific revision requirements.

---

## Getting Help and Support

Your success in this assignment is important! Here are the best ways to get help when you need it:

### 🗣️ Discussion Forum (Preferred for most questions)
- **Tag all posts with [A01]** so they're easy to find
- **Include specific details**: What you tried, what happened, what you expected
- **Add screenshots** for visual issues (error messages, layout problems)
- **Search first** - your question might already be answered
- **Help others** - if you solve a problem, share your solution

### 👥 Office Hours (For hands-on help)
- **Best for**: Complex technical issues, code review, conceptual questions
- **Come prepared**: Have your code open, know what specific issue you're facing
- **Bring specific questions**: "My navigation isn't working" is better than "my page is broken"
- **Schedule in advance** when possible

### 📚 Self-Help Resources
Before asking for help, try these resources:
- **MDN Web Docs**: https://developer.mozilla.org/en-US/docs/Web/HTML
  - Comprehensive, accurate documentation for every HTML element
- **W3C HTML Validator**: https://validator.w3.org/
  - Catches syntax errors and structural problems
- **WAVE Accessibility Checker**: https://wave.webaim.org/
  - Identifies accessibility issues and improvements

### 🚨 When to Ask for Help Immediately
Don't struggle alone with these issues:
- **Git problems** that might lose your work
- **File/folder structure confusion** early in the assignment
- **Accessibility requirements** you don't understand
- **Browser/environment issues** preventing you from completing the work

### 💡 How to Ask Great Questions
Help us help you by providing:
1. **Specific description** of what you're trying to do
2. **What you've tried** already
3. **Exact error messages** (copy/paste or screenshot)
4. **Your code** (relevant sections, not the entire file)
5. **Browser and environment** you're using

### ⏰ Response Time Expectations
- **Discussion forum**: Within 24 hours during weekdays
- **Office hours**: Immediate during scheduled times
- **Email**: 48 hours for non-urgent matters
- **Emergency technical issues**: Contact immediately

### 🤝 Peer Learning
You're encouraged to:
- **Discuss approaches** and problem-solving strategies
- **Share resources** you find helpful
- **Help debug** each other's technical issues
- **Study together** for concepts and best practices

Remember: The goal is learning, not just completing the assignment!

## Academic Integrity
Original work required; you may reference docs. Do not copy another student
