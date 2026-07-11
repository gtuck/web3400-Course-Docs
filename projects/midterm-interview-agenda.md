# Mandatory Midterm Status Review Interview Agenda

## Introduction

To ensure all students are progressing appropriately before the second half of the semester, I will conduct individual five-minute project review interviews covering **Projects 00–03**. These sessions will take place in my **Office Hours Zoom Room**: [https://weber.zoom.us/j/8013088825](https://weber.zoom.us/j/8013088825).

Please schedule your appointment through my **Google Appointment Schedule**: [https://calendar.app.google/1CeVqU8e2xfCpB9PA](https://calendar.app.google/1CeVqU8e2xfCpB9PA)

Each meeting is strictly five minutes and is intended for a brief demonstration of your progress. If an issue arises during your demonstration, please proceed to the next agenda item. For further assistance, schedule a separate appointment during my regular office hours.

---

## Student Preparation (Before Joining)

- Pull the latest `main` locally and confirm `projects/00`–`projects/03` are pushed to GitHub.
- Have your Project 03 app running locally (`php -S 0.0.0.0:8000 -t public` from the project root, or your dev container).
- Have your Git log ready: `git log --oneline -n 10`

---

## Checklist for Review

### Project 00: Configuration, Templates & Contact Form

- `config.php` centralizes site variables, PDO connection, and the `flash()` helper.
- Shared templates (`head.php`, `nav.php`, `footer.php`) used on every page.
- Contact Us form saves to the database with a prepared statement and shows a flash message.

### Project 01: Mini CMS (CRUD)

- Public list (`index.php`) and single-post (`blog_post.php`) pages driven by the `posts` table.
- Admin pages (`admin_blog.php`, `blog_create.php`, `blog_edit.php`, `blog_delete.php`) implement full CRUD.
- Forms follow the PRG (Post-Redirect-Get) pattern and use prepared statements.

### Project 02: MVC Refactor

- Monolithic script separated into Model, View, and Controller with PSR naming conventions.
- Application behavior unchanged after the refactor; concerns cleanly separated.

### Project 03: Router, Namespaces & Autoloading

- Front controller (`public/index.php`) routes all requests.
- `Router` dispatches routes defined in `src/Routes/index.php`; base `Controller` provides `render()`.
- Composer PSR-4 autoloading configured (`App\` → `src/`).
- Contact Us page works through the router (see `addPage.md`).

---

## Review Questions to Consider

1. Why did we refactor from procedural pages (Projects 00–01) to MVC (Projects 02–03)? *(Tests understanding of separation of concerns.)*
2. How do prepared statements protect against SQL injection? *(Assesses knowledge of security best practices.)*
3. Walk me through what happens from request to response in your Project 03 app. *(Tests understanding of the front controller and routing.)*
4. What challenges did you encounter, and how did you overcome them? *(Encourages reflection on problem-solving skills.)*

---

## Outcome

- **Approved:** The student demonstrated complete understanding and implementation.
- **Needs Revision:** The student needs to address identified issues before approval.
