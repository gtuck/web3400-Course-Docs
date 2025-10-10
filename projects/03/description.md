# Project 03 - Organizing MVC Files into Folders

Learn to organize your Model-View-Controller application using a professional folder structure with proper namespacing and file organization standards.

---

## Overview

In this project, you will take the MVC refactoring from Project 02 and organize it into a clean, scalable folder structure. You'll learn industry-standard practices for organizing PHP applications, following conventions used by professional frameworks.

**Key Learning Outcomes:**
- Organize Models and Controllers into separate folders using an `src/` directory
- Apply proper file and class naming conventions (singular vs. plural)
- Understand the relationship between database tables and Model class names
- Structure Views in a dedicated folder with descriptive naming
- Update file paths and `require` statements after reorganization

---

## Starting Point

You are starting with the MVC structure from Project 02:

```
project-02/
├── index.php           # Entry point
├── controller.php      # Controller class
├── model.php          # Model class
└── view.php           # HTML template
```

**Problems with this flat structure:**
- All files at the root level (messy as the app grows)
- Generic names like `model.php` and `controller.php` don't scale
- No clear organization for multiple models and controllers
- Doesn't follow industry standards for PHP applications
- Hard to find specific files in larger projects

---

## What You Will Build

You will reorganize the files into this professional structure:

```
project-03/
├── index.php
├── src/
│   ├── controllers/
│   │   └── products.php    # Products controller
│   └── models/
│       └── product.php     # Product model
└── views/
    └── products_index.php  # Products index view
```

**Same functionality, professional organization** = industry-standard structure!

---

## Understanding the Folder Structure

### `src/` Directory (Source)
- Standard practice for PHP applications
- Contains pure PHP classes (Models and Controllers)
- Separates business logic from presentation
- Easy to organize with namespaces later

### `src/models/` Directory
- Contains all Model classes
- Each model typically corresponds to a database table
- **Naming convention: SINGULAR**
  - `product.php` for the `products` table
  - `post.php` for the `posts` table
  - `contact.php` for the `contacts` table

### `src/controllers/` Directory
- Contains all Controller classes
- Each controller handles requests for a resource
- **Naming convention: PLURAL**
  - `products.php` handles product-related requests
  - `posts.php` handles post-related requests
  - `contacts.php` handles contact-related requests

### `views/` Directory
- Contains HTML templates with minimal PHP
- NOT inside `src/` because views aren't pure PHP classes
- More HTML than PHP code
- **Naming convention: `{resource}_{action}.php`**
  - `products_index.php` - list all products
  - `products_show.php` - show single product
  - `posts_index.php` - list all posts

---

## Naming Conventions Summary

| Component | Convention | Examples | Database Table |
|-----------|-----------|----------|----------------|
| **Model** | Singular, StudlyCaps class name | `Product`, `Post`, `Contact` | `products`, `posts`, `contacts` |
| **Model File** | Singular, lowercase | `product.php`, `post.php` | `products`, `posts` |
| **Controller** | Plural, StudlyCaps class name | `Products`, `Posts`, `Contacts` | N/A |
| **Controller File** | Plural, lowercase | `products.php`, `posts.php` | N/A |
| **View File** | `{resource}_{action}` | `products_index.php`, `posts_show.php` | N/A |

**Why this matters:** Following conventions makes your code predictable and easier for other developers to understand.

---

## Step-by-Step Instructions

### Step 1: Create the Folder Structure

Create the necessary directories for organizing your code.

**Create these folders:**
1. `src/` - for source code (PHP classes)
2. `src/models/` - for Model classes
3. `src/controllers/` - for Controller classes
4. `views/` - for View templates

```bash
mkdir -p src/models src/controllers views
```

**Result:** Empty folder structure ready for your organized files.

---

### Step 2: Rename and Move the Model

The Model handles product data, so it should be named `product.php` (singular).

**Tasks:**
1. Rename `model.php` to `product.php`
2. Move `product.php` into `src/models/`
3. Rename the class from `Model` to `Product` (StudlyCaps, singular)
4. Keep the `getData()` method unchanged

**Before:**
```php
<?php
// model.php

class Model
{
    public function getData(): array
    {
        // Database connection and query
        // ...
    }
}
```

**After:**
```php
<?php
// src/models/product.php

class Product
{
    public function getData(): array
    {
        // Database connection and query
        // ...
    }
}
```

**Convention reminder:** Model class names are **singular** and correspond to database tables.

---

### Step 3: Rename and Move the Controller

The Controller handles products (plural), so it should be named `products.php`.

**Tasks:**
1. Rename `controller.php` to `products.php`
2. Move `products.php` into `src/controllers/`
3. Rename the class from `Controller` to `Products` (StudlyCaps, plural)
4. Update the `require` path for the Model
5. Update the class name when creating a new Model object

**Before:**
```php
<?php
// controller.php

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

**After:**
```php
<?php
// src/controllers/products.php

class Products
{
    public function index()
    {
        require __DIR__ . "/../models/product.php";

        $product = new Product;

        $posts = $product->getData();

        require __DIR__ . "/../../views/products_index.php";
    }
}
```

**Important changes:**
- Class renamed: `Controller` → `Products`
- Model path: `"model.php"` → `__DIR__ . "/../models/product.php"`
- Model class: `new Model` → `new Product`
- View path: `"view.php"` → `__DIR__ . "/../../views/products_index.php"`

**Note about `__DIR__`:** This magic constant gives the absolute path of the current file's directory, making paths work regardless of where the script is run from.

---

### Step 4: Rename and Move the View

The View displays the products index page.

**Tasks:**
1. Rename `view.php` to `products_index.php`
2. Move `products_index.php` into `views/`
3. No changes needed to the HTML code inside

**View naming pattern:** `{resource}_{action}.php`
- `products_index` = show list of products
- Future examples: `products_show`, `products_edit`, `posts_index`

**File structure:**
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

**No code changes** - just moved and renamed!

---

### Step 5: Update index.php

Update the entry point to use the new file structure.

**Before:**
```php
<?php

require "controller.php";

$controller = new Controller;

$controller->index();
```

**After:**
```php
<?php

require "src/controllers/products.php";

$controller = new Products;

$controller->index();
```

**Changes:**
- Updated path: `"controller.php"` → `"src/controllers/products.php"`
- Updated class name: `Controller` → `Products`

---

## Complete File Structure

Here's what your organized project should look like:

```
project-03/
├── index.php                      # Entry point (5 lines)
├── src/                          # Source code directory
│   ├── controllers/
│   │   └── products.php          # Products class (plural)
│   └── models/
│       └── product.php           # Product class (singular)
└── views/
    └── products_index.php        # Products index view
```

### File Content Summary

#### [index.php](files/index.php)
Entry point - requires controller and calls `index()` method.

#### [src/controllers/products.php](files/src/controllers/products.php)
`Products` controller class - coordinates Model and View with updated paths.

#### [src/models/product.php](files/src/models/product.php)
`Product` model class - handles database connection and data retrieval.

#### [views/products_index.php](files/views/products_index.php)
View template - displays HTML with product data.

---

## Path Resolution with `__DIR__`

Understanding relative paths is crucial when organizing files into folders.

### What is `__DIR__`?
A PHP magic constant that contains the absolute path of the directory containing the current file.

### Why use `__DIR__`?
- Makes paths work regardless of where the script is run from
- More reliable than relative paths like `../`
- Prevents "file not found" errors

### Example from Controller

**File location:** `src/controllers/products.php`

**To require the Model (one level up, then into models/):**
```php
require __DIR__ . "/../models/product.php";
```
- `__DIR__` = `/full/path/to/project/src/controllers`
- `/../models/` = go up one level to `src/`, then into `models/`
- Result: `/full/path/to/project/src/models/product.php`

**To require the View (two levels up, then into views/):**
```php
require __DIR__ . "/../../views/products_index.php";
```
- `__DIR__` = `/full/path/to/project/src/controllers`
- `/../../views/` = go up two levels to project root, then into `views/`
- Result: `/full/path/to/project/views/products_index.php`

---

## Benefits of Organized Structure

### 1. Scalability
Easy to add new models and controllers:
```
src/
├── controllers/
│   ├── products.php
│   ├── posts.php
│   ├── contacts.php
│   └── users.php
└── models/
    ├── product.php
    ├── post.php
    ├── contact.php
    └── user.php
```

### 2. Convention Over Configuration
Following standard conventions means:
- Other developers instantly understand your structure
- Framework-like organization without a framework
- Easy to migrate to frameworks like Laravel or Symfony later

### 3. Clear Separation
- Pure PHP classes go in `src/`
- HTML-heavy templates go in `views/`
- Entry points stay at root level

### 4. Professional Standards
- Matches industry best practices
- Follows PSR recommendations
- Prepares you for autoloading and namespaces

### 5. Easy Navigation
Know exactly where to find things:
- Controller problem? → `src/controllers/`
- Model issue? → `src/models/`
- Display bug? → `views/`

---

## Singular vs. Plural Convention

### Why Models are Singular

A Model represents **one instance** of a resource:
- A single `Product` object
- A single `Post` object
- A single `Contact` object

**Corresponds to database table:**
- `products` table → `Product` model
- `posts` table → `Post` model
- `contacts` table → `Contact` model

### Why Controllers are Plural

A Controller handles **all requests** for a resource type:
- `Products` controller manages all product operations
- `Posts` controller manages all post operations
- `Contacts` controller manages all contact operations

**Common actions in a plural controller:**
- `index()` - list all products
- `show()` - display one product
- `create()` - show form to create product
- `store()` - save new product

---

## Implementation Notes

### PSR Standards Review
- **Classes:** StudlyCaps (`Product`, `Products`)
- **Methods:** camelCase (`getData()`, `index()`)
- **Files:** Lowercase to match class name concept
- **Folders:** Lowercase plural for collections

### Database Table Naming
Typically plural and lowercase:
- `products`
- `posts`
- `contacts`
- `order_items` (snake_case for multiple words)

### Testing After Each Step
After each step, test in the browser:
1. No PHP errors displayed
2. Posts still display correctly
3. Output matches original

**If you get "file not found" errors, check your paths!**

---

## Common Mistakes to Avoid

1. **Wrong folder names:** `controller/` instead of `controllers/` (missing 's')
2. **Wrong Model naming:** `Products` (plural) instead of `Product` (singular)
3. **Wrong Controller naming:** `Product` (singular) instead of `Products` (plural)
4. **Incorrect paths:** Forgetting to use `__DIR__` or wrong number of `../`
5. **Case sensitivity:** `Controllers/` vs `controllers/` matters on Linux/Mac
6. **View naming:** `product_index.php` (singular) instead of `products_index.php` (plural)
7. **Moving files but not updating paths:** Update all `require` statements!

---

## Grading Checklist

- **Folder structure created correctly**
  - `src/` folder exists at project root
  - `src/models/` folder exists
  - `src/controllers/` folder exists
  - `views/` folder exists at project root (not inside `src/`)

- **Model file organized**
  - File renamed to `product.php` (singular)
  - File moved to `src/models/`
  - Class renamed to `Product` (StudlyCaps, singular)
  - `getData()` method still works
  - Database code unchanged and functional

- **Controller file organized**
  - File renamed to `products.php` (plural)
  - File moved to `src/controllers/`
  - Class renamed to `Products` (StudlyCaps, plural)
  - Path to Model updated correctly (uses `__DIR__`)
  - Model instantiation uses `new Product`
  - Path to View updated correctly (uses `__DIR__`)

- **View file organized**
  - File renamed to `products_index.php`
  - File moved to `views/`
  - HTML and display code unchanged
  - Still uses `htmlspecialchars()` for security

- **index.php updated**
  - Path updated to `src/controllers/products.php`
  - Instantiation uses `new Products`
  - Calls `index()` method
  - Still clean and simple (~5 lines)

- **Application functionality**
  - Page loads without errors
  - Posts display correctly
  - Output identical to Project 02
  - All paths resolve correctly

- **Naming conventions followed**
  - Model: singular class name (`Product`)
  - Controller: plural class name (`Products`)
  - View: `{resource}_{action}` pattern
  - All classes use StudlyCaps
  - All methods use camelCase

---

## Test Locally

From your project directory:
```bash
php -S 0.0.0.0:8080
```

Visit in browser:
```
http://localhost:8080/projects/03/index.php
```

**Expected result:** Same output as Project 02 - posts display correctly with no errors.

---

## Key Takeaways

1. **Professional organization** uses `src/` for PHP classes, `views/` for templates
2. **Models** are singular (represent one resource) - `Product`, `Post`
3. **Controllers** are plural (handle all requests) - `Products`, `Posts`
4. **Views** follow `{resource}_{action}` pattern - `products_index.php`
5. Use **`__DIR__`** for reliable path resolution in nested folders
6. **Same functionality** + better organization = successful refactoring
7. Following **conventions** makes code predictable and professional

**Start with MVC → Organize into folders → Scale with confidence**

---

## Submit

Submit the direct URL to your Project 03 folder (replace YOUR-USER and repo name):
```
https://github.com/YOUR-USER/YOUR-REPO/blob/main/projects/03/
```

---

## Additional Resources

- [PHP Magic Constants Documentation](https://www.php.net/manual/en/language.constants.magic.php)
- [PSR-4: Autoloader Standard](https://www.php-fig.org/psr/psr-4/)
- [Laravel Framework Structure](https://laravel.com/docs/structure) (similar conventions)
