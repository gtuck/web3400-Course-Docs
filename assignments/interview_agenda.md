### **Mandatory Assignment Status Review Interview Agenda**

#### **Introduction**
I will conduct individual five-minute project review interviews to ensure everyone is on track before we start the first Project. These reviews will be held in my [**Office Hours Zoom Room**:](https://weber.zoom.us/j/8013088825).

Please schedule your appointment on my [**Google Appointment Schedule**](https://calendar.app.google/3vPCRWodiuWML1HZ6).

So that you know, the meeting is only five minutes long and is not meant to be a troubleshooting session but a simple demonstration of your progress. If something doesn't work correctly, move on to the next agenda item. For additional help, sign up for a separate appointment during my office hours.

---

### **Checklist for Review**

#### **1. General Setup**
- [ ] Confirm that the `04` folder has been correctly copied from `03` using:
  - `cp -r 03 04`
- [ ] Verify that the folder is staged, committed, and pushed to the GitHub repository with the correct commit message.

#### **2. Folder and File Structure**
- [ ] Confirm the existence of a `templates` folder inside the `04` directory.
- [ ] Ensure the following files are present in the `templates` folder:
  - `head.php`
  - `nav.php`
  - `footer.php`

#### **3. Template File Content**
- **`head.php`:**
  - [ ] Does it contain the `<head>` section from the original `index.html`?
  - [ ] Confirm inclusion of meta tags, stylesheets, and scripts.
- **`nav.php`:**
  - [ ] Verify it contains the navigation (`<nav>`) and hero sections.
  - [ ] Confirm alignment with the example provided in the assignment.
- **`footer.php`:**
  - [ ] Ensure the footer section is properly transferred and formatted.

#### **4. Main File Conversion**
- [ ] Confirm the original `index.html` was replaced with `index.php`.
- [ ] Verify that `index.php` correctly includes:
  - `templates/head.php`
  - `templates/nav.php`
  - `templates/footer.php`
- [ ] Check for placeholder content (e.g., `<h1>` and `<h2>` sections) between the includes.

#### **5. Functionality**
- [ ] Confirm the web page runs correctly in the browser.
- [ ] Verify the inclusion files (`head.php`, `nav.php`, `footer.php`) work seamlessly.

#### **6. Git Workflow**
- [ ] Confirm proper use of Git commands:
  - `git add 04`
  - `git commit -m "Updated assignment 04"`
  - `git push`
- [ ] Verify the repository contains the updated `04` folder.

#### **7. Submission**
- [ ] Verify that the GitHub URL is submitted in the required format:
  ```
  https://github.com/[your-account-name]/[your-web3400-repo]/blob/main/assignments/04/
  ```
---

### **Additional Review Questions**
1. Can you explain the purpose of creating separate template files in this assignment?
2. How does this approach improve code reusability and maintainability?
3. What challenges did you face while completing this assignment, and how did you resolve them?

---

### **Outcome**
- [ ] Approved: The student demonstrated complete understanding and implementation.
- [ ] Needs Revision: The student needs to address identified issues before approval.
