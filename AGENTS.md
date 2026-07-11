# AGENTS.md

This file provides guidance to all AI coding assistants (Claude Code, Cursor, Windsurf, Gemini, Cody, etc.) when working with code in this repository. It is the **single canonical instruction file**: `CLAUDE.md`, `CURSOR.md`, `GEMINI.md`, `WINDSURF.md`, and their dot-directory counterparts are symlinks to this file. Edit only this file.

## Repository Purpose

This is a course documentation repository for **WEB 3400 - Web Application Development**, a PHP/MySQL web development course at Weber State University. The repository contains:

- Course syllabus and schedule (index.md, schedule.md)
- Assignment descriptions and starter files (assignments/)
- Project descriptions, starter code, and complete reference implementations (projects/)
- PHP code examples and syntax reference (PHP-Code-Examples.md, php_code_examples_v2.md)
- Grading rubrics (CSV files in project directories)

## Current Semester (update every term)

<!-- UPDATE THIS BLOCK AT THE START OF EACH SEMESTER -->
- **Term:** Fall 2026 — classes Mon Aug 24 through Fri Dec 4, 2026
- **Meetings:** Monday & Wednesday, 11:00 AM–12:15 PM, synchronous virtual via Zoom (class meeting ID changes each semester; Zoom link posted in Canvas)
- **Office hours:** Monday & Wednesday, 8:00–9:00 AM and 12:30–2:00 PM, by appointment, in the instructor's personal Zoom room (`weber.zoom.us/j/8013088825`)
- **No-class days:** Labor Day (Sep 7), Fall Break (Fri Oct 9), Thanksgiving (Nov 26–27)
- **Student repo name:** `web3400-fall26` (updated throughout gettingStarted, A00–A04, P00, and the interview agenda)
- **Known gaps:** `welcome.md` still shows the Spring 2026 class meeting ID; schedule.md event titles need the Fall 2026 CRN once created

## Repository Structure

```
web3400-Course-Docs/
├── index.md              # Course syllabus
├── schedule.md           # Course schedule with due dates
├── welcome.md            # Day 1 information
├── assignments/          # Smaller assignments (00-04)
│   └── */description.md
├── projects/             # Major projects (00-08, fp)
│   ├── 00-08/           # Individual projects
│   │   ├── description.md    # Project requirements
│   │   ├── rubric.csv        # Grading rubric (all projects)
│   │   ├── files/            # Starter code (02-04)
│   │   └── complete/         # Reference implementation (01, 04+)
│   └── fp/              # Final project
└── PHP-Code-Examples.md # PHP syntax reference
```

## Project Progression (MVC Framework Evolution)

The projects build a PHP MVC framework from scratch:

1. **Projects 00-01**: Basic PHP, procedural code, shared templates, mini CMS (CRUD with PDO)
2. **Project 02**: MVC refactor - separating Model, View, and Controller concerns
3. **Project 03**: MVC architecture - Router, front controller pattern, namespaces, PSR-4 autoloading
4. **Project 04**: Environment variables (phpdotenv), Database helper, BaseModel (Active Record pattern), model generator script
5. **Project 05**: View templating engine (vanilla PHP), CSRF protection, Validator class, RESTful routing
6. **Project 06**: Authentication/authorization (sessions, password hashing, roles)
7. **Project 07**: Content Management System (Posts CMS)
8. **Project 08**: Post engagement - likes, favorites, and moderated comments
9. **Final Project**: Comprehensive admin dashboard (starts from a completed Project 08)

## Maintainer Workflow

### Update Flow
- Start from `main`: `git pull origin main`.
- Use short-lived branches when drafting major syllabus/assignment revisions; fast edits can stay on `main` but keep commits scoped (e.g., `feat: clarify Project 06 schema`).
- After merging, tag notable milestones (`git tag -a fall26-v1`) so you can roll Canvas embeds back if needed.

### Content Structure Reminders
- Root-level Markdown files power Canvas iframes (`index.md`, `welcome.md`, `schedule.md`).
- `_config.yml` disables Liquid for `assignments/`, `projects/`, and `tutorials/`; stick to plain Markdown/HTML in those folders unless you update the config.
- Reference implementations live under `projects/*/(files|complete)` with their own `composer.json`; only install Composer deps when actively testing those samples.
- Final project docs live in `projects/fp/` (description, rubric, and `final-interview-agenda.md`). Keep the final interview agenda aligned with the FP Requirements Checklist.
- Interview agendas: `assignments/interview_agenda.md` (assignment status review, week 5), `projects/midterm-interview-agenda.md` (midterm review of Projects 00-03), `projects/fp/final-interview-agenda.md` (final project review).

### Editing Conventions
- One `#` per page, Title Case headings, kebab-case filenames.
- When pasting long instructions (e.g., assignment scaffolds), follow with a concise "Requirements" list so students know what must be original.
- Keep schedule dates synchronized across `schedule.md`, Canvas, and any announcements. When in doubt, edit `schedule.md` first, then copy to Canvas.

### Preview & Publish
- Local preview (optional but safest): `jekyll serve --livereload` and check http://localhost:4000 for layout issues before pushing.
- GitHub Pages publishes from `main`; allow a few minutes after pushing before testing Canvas iframes.
- Verify embeds in Canvas: open each module page, confirm iframes load without mixed-content warnings, and note any scroll issues.

### Quality Gates Before Sharing
- Run `rg TODO -n` at the repo root; resolve or annotate anything that shouldn't ship to students.
- For projects/assignments referencing databases or PHP helpers, ensure the referenced files exist in `projects/*/complete` and that code samples match reality.
- Re-read the most recent audit in `.reports/` (currently `continuity-quality-audit-2026-07-11.md`) when planning significant restructures so you keep known gaps in view.

## Key Architecture Patterns

### MVC Structure (Projects 03+)
```
public/
  index.php          # Front controller (bootstraps app, routes requests)
src/
  Router.php         # Route registration and dispatch
  Controller.php     # Base controller (render, flash, redirect, CSRF)
  Support/
    Database.php     # PDO singleton/helper
    View.php         # Template engine (P05+)
    Validator.php    # Validation rules (P05+)
  Models/
    BaseModel.php    # Active Record base (static CRUD methods)
    {Entity}.php     # Generated models extend BaseModel
  Controllers/
    {Entity}Controller.php
  Views/
    layouts/         # Layout templates
    partials/        # Reusable components
    {view}.php       # View templates
  Routes/
    index.php        # Route definitions
scripts/
  generate-model.php # Model generator from DB tables
```

### BaseModel Pattern (Projects 04+)

Models extend `BaseModel` and use **static methods** for CRUD operations:

```php
// Correct usage (static methods)
$posts = Post::all(limit: 10, orderBy: 'created_at DESC');
$post = Post::find($id);
$id = Post::create(['title' => '...', 'body' => '...']);
Post::update($id, ['title' => '...']);
Post::delete($id);

// Incorrect (do not instantiate)
$post = new Post(); // Wrong!
```

Key properties in model classes:
- `protected static string $table` - Database table name
- `protected static string $primaryKey` - Primary key column (default: 'id')
- `protected static array $fillable` - Mass assignment whitelist

### View Templating (Project 05+)

Templates use the `View` class with `$this` context:

```php
// In views
<?php $this->layout('layouts/main'); ?>
<?php $this->start('content'); ?>
  <h1><?= $this->e($title) ?></h1>
<?php $this->end(); ?>

// Methods available in templates:
$this->layout('path')           // Set layout
$this->start('section')         // Begin section
$this->end()                    // End section
$this->section('section')       // Yield section in layout
$this->insert('partial', $data) // Include partial
$this->e($value)                // Escape output (XSS protection)
```

### Security Practices (Project 05+)

- **CSRF Protection**: All POST/PUT/DELETE/PATCH forms must include CSRF token
- **XSS Prevention**: Always use `$this->e()` for dynamic output in views
- **SQL Injection**: BaseModel uses prepared statements; always use parameterized queries
- **Mass Assignment Protection**: Models whitelist fillable columns in `$fillable` array
- **Password Hashing**: Use `password_hash()` and `password_verify()` (Project 06+)

### Database Connection

Environment variables (via phpdotenv) in `.env`:
```
DB_HOST=db
DB_NAME=web3400
DB_USER=web3400
DB_PASS=password
DB_CHARSET=utf8mb4
```

Access via `Database::pdo()` helper, which returns configured PDO instance.

## Common Development Commands

### Model Generation
```bash
# Generate model from database table
php scripts/generate-model.php table_name
# Example: php scripts/generate-model.php contact_us
# Creates: src/Models/Contact.php
```

### Composer
```bash
composer dump-autoload  # Regenerate autoloader after adding classes
composer require pkg    # Add dependency
```

### Development Server
```bash
# From project root (where public/ exists)
php -S 0.0.0.0:8000 -t public
```

## Important Conventions

### Array Syntax
Use short array syntax consistently:
```php
// Correct
$fillable = ['name', 'email', 'message'];

// Incorrect
$fillable = array('name', 'email', 'message');
```

### Routing Pattern
Routes defined in `src/Routes/index.php`:
```php
$router = new Router();
$router->get('/', HomeController::class, 'index');
$router->post('/contact', ContactController::class, 'submit');
$router->dispatch();
```

### Controller Pattern
```php
class ExampleController extends Controller
{
    public function show()
    {
        $this->render('view-name', ['data' => $value]);
    }

    public function submit()
    {
        // Validate CSRF (P05+)
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $this->flash('Security error', 'is-danger');
            $this->redirect('/path');
        }

        // Process form...

        // PRG pattern (Post-Redirect-Get)
        $this->flash('Success message', 'is-success');
        $this->redirect('/path');
    }
}
```

### Validation (Project 05+)
```php
use App\Support\Validator;

$errors = Validator::validate($data, [
    'email' => 'required|email',
    'name'  => 'required|max:255',
]);

if (!empty($errors)) {
    $flat = Validator::flattenErrors($errors);
    foreach ($flat as $error) {
        $this->flash($error, 'is-warning');
    }
}
```

## File Naming Conventions

- Models: Singular, PascalCase (e.g., `Post.php`, `Contact.php`, `BlogPost.php`)
- Controllers: Singular + "Controller" (e.g., `HomeController.php`, `ContactController.php`)
- Views: Lowercase, match route or action (e.g., `index.php`, `contact.php`, `show.php`)
- Tables: Plural, snake_case (e.g., `posts`, `contact_us`, `blog_posts`)

## Environment Setup

Projects typically require:
- PHP 8.0+
- MySQL/MariaDB
- Composer
- Docker Desktop (recommended for consistent environment)

Students use Docker Development Containers with a LAMP stack (Linux-Apache-MySQL-PHP).

## Git Practices

- Never commit `.env` files (use `.env.example` as template)
- Commit `vendor/` directory is acceptable for this course (simplifies deployment)
- Student repositories are individual (not forked from this repo)

## Reference Implementations

Projects 01 and 04+ (plus fp) have a `complete/` directory with a working reference implementation. These are for instructor reference and should match the requirements in `description.md`. Projects 06+ include a `schema.sql` extracted from the description's SQL blocks — keep the two in sync. Reference `.env` files are never tracked in git (each `complete/` has a `.gitignore`); use `.env.example` as the template.

## Rubrics

Grading rubrics are CSV files (`rubric.csv`) in every project directory (00 through 08 and fp). They list specific criteria and point values for assessment. Canvas rubric CSVs must include a `Rubric Name` column on every row and rating headers (e.g., Full/No Credit). Use `import_rubric_template.csv` as the pattern when updating project rubrics.

## Code Generator Script

The `scripts/generate-model.php` tool:
- Introspects database tables via INFORMATION_SCHEMA
- Generates model classes extending BaseModel
- Auto-detects primary key
- Excludes timestamp columns (created_at, updated_at, deleted_at) from fillable
- Converts plural table names to singular class names (posts → Post)
- Outputs clean array syntax and documentation

## Slug Helper in Base Controller

Project 07+ includes a `slugify()` helper method in the base Controller class for generating URL-friendly slugs from strings (used for post slugs in Projects 07+).

## CSS Framework

Projects use **Bulma CSS** (https://bulma.io) loaded via CDN. Common classes:
- `.container` - Centered container
- `.section` - Padded section
- `.box` - Card-like container
- `.button.is-primary` - Primary button
- `.notification.is-success` - Success message
- `.field`, `.control`, `.input` - Form elements

Font Awesome icons also loaded via CDN.

## When Working on Project Code

1. Always check which project number you're working on (architecture differs significantly)
2. For Projects 04+: Use BaseModel static methods, not instantiation
3. For Projects 05+: Use View templating, CSRF protection, and Validator class
4. Follow the security practices (CSRF, XSS escaping, prepared statements, mass assignment protection)
5. Test with PHP's built-in server: `php -S 0.0.0.0:8000 -t public`
6. Regenerate autoloader after adding classes: `composer dump-autoload`

## Data & Security Notes

- Never commit populated `.env` files or student-submitted artifacts; keep placeholders as `.env.example`.
- Sanitize SQL dumps and credentials inside `projects/**/sql` before pushing.
- Avoid enabling Liquid tags in content folders unless required; doing so could expose unintended templated data on GitHub Pages.

## Specialized Agents

This repository includes specialized AI agents in the `.agents/` directory for specific tasks:

- **course-design-reviewer** (`.agents/course-design-reviewer.md`): Reviews course materials, syllabi, learning outcomes, assignment descriptions, module structure, and curriculum design for pedagogical effectiveness and alignment.

- **php-assignment-architect** (`.agents/php-assignment-architect.md`): Creates, revises, and refines PHP project assignment descriptions and their corresponding solution implementations. Use when developing new assignments or updating existing project requirements.

### Multi-LLM Single-Source Architecture

Both the agents and this instruction file are maintained in one canonical location and exposed to every AI assistant via symlinks:

```
AGENTS.md                   # Canonical instructions (this file)
CLAUDE.md   -> AGENTS.md    # Claude Code
CURSOR.md   -> AGENTS.md    # Cursor IDE
GEMINI.md   -> AGENTS.md    # Google Gemini
WINDSURF.md -> AGENTS.md    # Windsurf IDE
.claude/CLAUDE.md     -> ../AGENTS.md
.cursor/CURSOR.md     -> ../AGENTS.md
.gemini/GEMINI.md     -> ../AGENTS.md
.windsurf/WINDSURF.md -> ../AGENTS.md

.agents/                    # Canonical agents
├── course-design-reviewer.md
└── php-assignment-architect.md
.claude/agents   -> ../.agents
.cursor/agents   -> ../.agents
.gemini/agents   -> ../.agents
.windsurf/agents -> ../.agents
```

This architecture ensures a single source of truth, universal compatibility, and easy maintenance: update once, available everywhere. **Never edit the symlinked copies; edit `AGENTS.md` or `.agents/*.md` directly.**

Note: `AGENTS.md`, its symlinks, and `.agents/` are tracked in git and travel with clones of this repo. Per `.gitignore`, `.claude/settings.local.json` and `.reports/` remain local only.

## AI-Generated Reports

**IMPORTANT**: All AI-generated reports, analyses, and review documents must be saved to the `.reports/` directory. This directory is gitignored to keep AI-generated content local only.

### Report Naming Convention
Use descriptive names with dates when applicable:
- `.reports/course-review-report.md`
- `.reports/project-analysis-YYYY-MM-DD.md`
- `.reports/security-audit-report.md`
- `.reports/code-review-YYYY-MM-DD.md`

### When to Save Reports
Always save to `.reports/` when:
- Running the `course-design-reviewer` agent
- Running the `php-assignment-architect` agent
- Generating any analysis, audit, or review document
- Creating documentation that summarizes AI findings or recommendations

Reports are point-in-time snapshots; check the date in the filename and verify findings are still current before acting on older reports.
