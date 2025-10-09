# Project 02 - Refactoring to MVC Pattern
Learn the Model-View-Controller (MVC) design pattern by refactoring a monolithic PHP script into a well-organized, maintainable application with separated concerns.

---

## Overview
In this project, you will take a single PHP file that mixes database logic and HTML presentation, and refactor it to follow the MVC (Model-View-Controller) pattern. This teaches you how to separate concerns, making your code more maintainable, testable, and scalable.

**Key Learning Outcomes:**
- Understand the MVC design pattern and its benefits
- Separate data logic (Model), presentation (View), and application flow (Controller)
- Follow PHP Standard Recommendations (PSR) for naming conventions
- Practice refactoring without changing application behavior

---

## Starting Point
You are provided with a monolithic PHP script ([starting_index.php](files/starting_index.php)) that:
- Connects to a MySQL database
- Queries the `posts` table
- Displays posts in HTML format
- Mixes database logic and HTML in a single file

**Problems with this approach:**
- Mixed concerns: Database logic + HTML in one file
- Hard to maintain: Changes require editing multiple sections
- Not reusable: Logic can't be shared across pages
- Testing difficulties: Can't test logic independently
- Violates separation of concerns principle

---

## What is MVC?

The **Model-View-Controller** pattern separates an application into three interconnected components:

### Model
**Handles Data**
- Database connections and queries
- Business logic
- Data validation
- Returns data to the Controller

### View
**Handles Display**
- HTML templates
- Presentation logic
- User interface elements
- Output formatting
- Should contain minimal PHP (only for display loops and escaping)

### Controller
**Handles Flow**
- Coordinates Model and View
- Processes user requests
- Contains application logic
- Makes decisions about what to display

### Flow of Execution
```
User Request  ➡️ Controller  ➡️ Model (fetch data)  ➡️ Controller  ➡️ View (display)  ➡️ Response
```

---

## What You Will Build

You will refactor the monolithic script into four separate files:

```
project-02/
   index.php           # Entry point (3 lines!)
   controller.php      # Controller class
   model.php          # Model class
   view.php           # HTML template
```

**Same output, better organized code** = successful refactoring!

---

## Step-by-Step Instructions

### Step 1: Create the Model

Create a new file `model.php` that handles all data operations.

**Requirements:**
- Create a class named `Model` (note the StudlyCaps)
- Add a public method `getData()` that returns an array
- Move the database connection and query code from the original script
- Return the fetched data array

**PHP Naming Conventions (PSR Standards):**
- Classes: `StudlyCaps` (e.g., `Model`, `ProductController`)
- Methods: `camelCase` (e.g., `getData`, `findById`)

**Example structure:**
```php
<?php

class Model
{
    public function getData(): array
    {
        // Database connection code here
        // Query execution here
        // Return fetched data
    }
}
```

**What to move into `getData()`:**
1. Database connection variables (host, dbname, username, password)
2. DSN (Data Source Name) string
3. PDO object creation with error mode and fetch mode
4. SQL query execution
5. `fetchAll()` to get results
6. Return the results array

---

### Step 2: Update Index to Use Model

Modify your `index.php` to use the new Model class instead of having database code mixed in.

**Before (monolithic):**
```php
<?php
// Database connection
$host = 'db';
$dbname = 'web3400';
$username = 'web3400';
$password = 'password';

$dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$stmt = $pdo->query("SELECT * FROM posts");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<!-- HTML here -->
```

**After (using Model):**
```php
<?php
require "model.php";

$model = new Model;

$posts = $model->getData();
?>
<!DOCTYPE html>
<!-- HTML here -->
```

**Test at this point:** Open the page in your browser. It should work exactly as before. Nothing has changed from the user's perspective!

---

### Step 3: Create the View

Create a new file `view.php` that contains only the HTML presentation.

**Requirements:**
- Move all HTML from `index.php` into `view.php`
- Keep the `foreach` loop that displays posts
- Keep the `htmlspecialchars()` escaping for security
- Remove all database logic (it's now in the Model)

**The View should contain:**
```php
<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>Blog Posts</h1>

<?php foreach ($posts as $post): ?>

    <h2><?= htmlspecialchars($post["title"]) ?></h2>
    <p><?= htmlspecialchars($post["body"]) ?></p>

<?php endforeach; ?>

</body>
</html>
```

**Note:** The `$posts` variable will be available because the Controller will call this View after fetching the data.

---

### Step 4: Create the Controller

Create a new file `controller.php` that coordinates the Model and View.

**Requirements:**
- Create a class named `Controller`
- Add a public method `index()`
- Inside `index()`:
  1. Require the Model file
  2. Create a Model object
  3. Call `getData()` and store in `$posts`
  4. Require the View file

**Example structure:**
```php
<?php

class Controller
{
    public function index()
    {
        require "model.php";

        $model = new Model;

        $posts = $model->getData();

        require "view.php";
    }
}
```

**Why this works:** When the Controller requires `view.php`, the `$posts` variable is already in scope, so the View can use it in the `foreach` loop.

---

### Step 5: Simplify Index (Final Step)

Update `index.php` to be just an entry point that delegates everything to the Controller.

**Final `index.php` (complete code):**
```php
<?php

require "controller.php";

$controller = new Controller;

$controller->index();
```

**That's it!** Just 5 lines (3 lines of actual logic). Everything is properly delegated.

---

## Complete File Structure and Code

### [index.php](files/index.php)
Entry point - creates Controller and calls `index()` method.

### [controller.php](files/controller.php)
Controller class - coordinates Model and View.

### [model.php](files/model.php)
Model class - handles database connection and data retrieval.

### [view.php](files/view.php)
View template - displays HTML with post data.

### [starting_index.php](files/starting_index.php)
Original monolithic script (your starting point).

---

## Implementation Notes

### PSR Naming Standards
- **Classes:** `StudlyCaps` - First letter of each word capitalized
  - `Model`, `Controller`, `PostModel`
  - `model`, `CONTROLLER`, `post_model`

- **Methods:** `camelCase` - First word lowercase, subsequent words capitalized
  - `getData()`, `findById()`, `updatePost()`
  - `get_data()`, `GetData()`, `UPDATE_POST()`

### Security Best Practices
- Always use `htmlspecialchars()` when outputting user data
- Use `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` for proper error handling
- Keep database credentials secure (in production, use environment variables)

### Testing Your Refactoring
After each step, test in the browser to ensure:
1. No errors are displayed
2. Posts still display correctly
3. Output is identical to the original script

**Successful refactoring means:** Same functionality, better code organization.

---

## Benefits of MVC

### 1. Separation of Concerns
Each component has **one job** and does it well:
- Model: data
- View: presentation
- Controller: flow

### 2. Maintainability
Easy to find and fix issues - you know exactly where to look:
- Database problem? Check the Model
- Display issue? Check the View
- Logic error? Check the Controller

### 3. Reusability
- The same Model can be used by multiple Controllers
- The same View can display data from different sources
- Controllers can be extended for different features

### 4. Testability
- Test Model logic independently of HTML
- Test data retrieval without rendering views
- Unit tests become much easier

### 5. Team Collaboration
- Frontend developers work on Views
- Backend developers work on Models
- Different team members can work simultaneously without conflicts

### 6. Scalability
- Easy to add new features without breaking existing code
- Clear structure makes the codebase easier to understand
- New developers can quickly learn the architecture

---

## Grading Checklist

- **Project 02 exists** at `projects/02/` and runs without errors
  - **`model.php` created** with a `Model` class
  - Class name uses StudlyCaps (`Model`, not `model`)
  - Contains `getData()` method that returns an array
  - Method uses camelCase naming
  - Database connection code is inside `getData()`
  - Query retrieves all posts from database
  - Returns data using `fetchAll(PDO::FETCH_ASSOC)`

- **`view.php` created** with HTML template only
  - Contains complete HTML document structure
  - Has `foreach` loop to display posts
  - Uses `htmlspecialchars()` to escape output
  - No database logic present
  - Uses `$posts` variable from Controller

- **`controller.php` created** with a `Controller` class
  - Class name uses StudlyCaps
  - Contains `index()` method (camelCase)
  - Requires `model.php`
  - Creates Model instance
  - Calls `getData()` and stores result
  - Requires `view.php` after getting data

- **`index.php` simplified** to entry point only
  - Requires `controller.php`
  - Creates Controller instance
  - Calls `index()` method
  - Contains no database or HTML code
  - Is clean and simple (about 5 lines total)

- **Functionality preserved**
  - Page displays same output as original script
  - Posts display with title and body
  - No errors when loading the page
  - Browser output is identical to starting point

- **Code quality**
  - Follows PSR naming conventions
  - Proper indentation and formatting
  - Comments are clear (if included)
  - No unused code or files

---

## Common Mistakes to Avoid

1. **Incorrect class naming:** `class model` instead of `class Model`
2. **Incorrect method naming:** `function get_data()` instead of `function getData()`
3. **Forgetting return type:** Add `: array` to `getData()` method signature
4. **Wrong variable name:** The View expects `$posts`, not `$data` or something else
5. **Missing `require` statements:** Each file must require its dependencies
6. **Testing too late:** Test after each step, not just at the end
7. **Changing functionality:** The output should be identical to the original

---

## Test Locally

From your project directory:
```bash
php -S 0.0.0.0:8080
```

Visit in browser:
```
http://localhost:8080/projects/02/index.php
```

**Expected result:** A page displaying post titles and bodies, identical to the original monolithic script.

---

## Key Takeaways

1. **MVC separates** data (Model), display (View), and logic (Controller)
2. **Models** handle database operations and return data
3. **Views** contain only HTML and presentation logic
4. **Controllers** coordinate between Models and Views
5. **Refactoring** improves code without changing behavior
6. Follow **PSR standards**: StudlyCaps for classes, camelCase for methods
7. Same output + better organized code = **successful refactoring**

**Start simple  ➡️ Refactor to MVC  ➡️ Scale confidently**

---

## Submit

Submit the direct URL to your Project 02 folder (replace YOUR-USER and repo name):
```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/02/
```

---

## Additional Resources

- [PHP-FIG PSR Standards](https://www.php-fig.org/psr/)
- [Lecture Slides](files/slides.md)
- [MVC Pattern Documentation](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
