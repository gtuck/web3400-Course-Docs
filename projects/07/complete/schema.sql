-- Project 07 - posts table + seed data
-- Extracted from description.md; keep in sync.

DROP TABLE IF EXISTS posts;

CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  author_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  excerpt VARCHAR(255) NULL,
  body MEDIUMTEXT NOT NULL,
  featured_image VARCHAR(255) NOT NULL DEFAULT 'https://picsum.photos/200',
  status ENUM('draft','published','archived','deleted') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  is_featured BOOLEAN NOT NULL DEFAULT 0,
  favs INT UNSIGNED NOT NULL DEFAULT 0,
  likes INT UNSIGNED NOT NULL DEFAULT 0,
  comments_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id),
  INDEX idx_status (status),
  INDEX idx_published_at (published_at)
);

INSERT INTO posts (
  author_id,
  title,
  slug,
  excerpt,
  body,
  featured_image,
  status,
  published_at,
  favs,
  likes,
  comments_count,
  is_featured
) VALUES
  (
    1,
    'Getting Started with PHP',
    'getting-started-with-php',
    'A beginner’s guide to understanding PHP fundamentals and syntax.',
    'PHP is a popular scripting language used primarily for web development. In this post, we’ll explore how to set up a local environment, write your first PHP script, and understand variables and functions.',
    'https://picsum.photos/seed/php/200',
    'published',
    NOW(),
    5,
    10,
    2,
    1
  ),
  (
    1,
    'Mastering MySQL Joins',
    'mastering-mysql-joins',
    'Learn how to use INNER, LEFT, and RIGHT joins effectively in MySQL.',
    'In relational databases, joins allow you to combine data from multiple tables. This tutorial covers common join types and best practices for optimizing queries.',
    'https://picsum.photos/seed/mysql/200',
    'published',
    NOW(),
    3,
    8,
    1,
    0
  ),
  (
    1,
    'Building a Blog with PHP and MySQL',
    'building-a-blog-with-php-and-mysql',
    'Step-by-step guide to building your own dynamic blog system using PHP and MySQL.',
    'This post walks through database setup, CRUD operations, and routing for a simple yet functional blog system using PHP and MySQL.',
    'https://picsum.photos/seed/blog/200',
    'published',
    NOW(),
    7,
    15,
    4,
    1
  ),
  (
    1,
    'Understanding RESTful APIs',
    'understanding-restful-apis',
    'A clear introduction to REST architecture and API best practices.',
    'APIs are at the heart of modern web applications. In this post, we discuss the principles of REST, HTTP methods, and how to build and consume APIs effectively.',
    'https://picsum.photos/seed/api/200',
    'draft',
    NULL,
    0,
    0,
    0,
    0
  ),
  (
    1,
    'Debugging PHP Applications',
    'debugging-php-applications',
    'Tips and tools for debugging PHP code efficiently.',
    'Debugging is an essential skill for every developer. Learn how to use built-in PHP error handling, Xdebug, and logging to identify and resolve common issues.',
    'https://picsum.photos/seed/debug/200',
    'archived',
    '2024-12-15 10:00:00',
    2,
    5,
    0,
    0
  );
