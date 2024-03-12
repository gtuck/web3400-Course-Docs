# Simple E-commerce System (Project 05)

In this project, you will develop a simple e-commerce system that allows users to manage customers, orders, and products. You'll follow a structured approach to ensure the e-commerce system functions correctly and provides a good user experience.

## Copy Project 04 to the Project 05 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Set Up the Database

Before coding, you must set up the database tables to store customers, orders, and products. Use the following SQL statements to create the tables:

```sql
-- Table structure for table `customers`
CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `orders`
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `products`
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `customers`
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

-- Indexes for table `orders`
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

-- Indexes for table `products`
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT for table `customers`
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- AUTO_INCREMENT for table `orders`
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- AUTO_INCREMENT for table `products`
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Foreign keys for table `orders`
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;
```

## Update the `config.php` file

**PHP**: Open your `config.php` file and add the following function at the end of the file to format currency:

```php
function format_currency($amount)
{
    return '$' . number_format($amount, 2);
}
```

This function formats a number as a currency string.

## Create the `customer.php` file

**HTML Structure**: Add the following HTML structure to your `customer.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title"><?= $customer['name'] ?></h1>
    <div class="box">
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong>Email:</strong> <?= $customer['email'] ?><br>
                        <strong>Address:</strong> <?= $customer['address'] ?><br>
                        <strong>Registered on:</strong> <?= date('F j, Y', strtotime($customer['created_at'])) ?>
                    </p>
                </div>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `customer.php` file. Your finished file will fetch customer data from the database and display it.

```php
<?php
// Step 1: Include config.php file

// Step 2: Check if the $_GET['id'] exists; if it does, get the customer record from the database and store it in the associative array named $customer.
// SQL example: SELECT * FROM customers WHERE id = ?

// Step 3: If a customer with that ID does not exist, display the message "A customer with that ID did not exist."
?>
```

## Create the `order.php` file

**HTML Structure**: Add the following HTML structure to your `order.php` file.

```html
<!-- BEGIN YOUR CONTENT

 -->
<section class="section">
    <h1 class="title">Order #<?= $order['id'] ?></h1>
    <div class="box">
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong>Customer:</strong> <?= $order['customer_name'] ?><br>
                        <strong>Total Price:</strong> <?= format_currency($order['total_price']) ?><br>
                        <strong>Status:</strong> <?= $order['status'] ?><br>
                        <strong>Ordered on:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?>
                    </p>
                </div>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `order.php` file. Your finished file will fetch order data from the database and display it.

```php
<?php
// Step 1: Include config.php file

// Step 2: Check if the $_GET['id'] exists; if it does, get the order record from the database and store it in the associative array named $order.
// SQL example: SELECT orders.*, customers.name AS customer_name FROM orders JOIN customers ON orders.customer_id = customers.id WHERE orders.id = ?

// Step 3: If an order with that ID does not exist, display the message "An order with that ID did not exist."
?>
```

## Create the `product.php` file

**HTML Structure**: Add the following HTML structure to your `product.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title"><?= $product['name'] ?></h1>
    <div class="box">
        <article class="media">
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong>Description:</strong> <?= $product['description'] ?><br>
                        <strong>Price:</strong> <?= format_currency($product['price']) ?><br>
                        <strong>Stock:</strong> <?= $product['stock'] ?> units<br>
                        <strong>Added on:</strong> <?= date('F j, Y', strtotime($product['created_at'])) ?>
                    </p>
                </div>
            </div>
        </article>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `product.php` file. Your finished file will fetch product data from the database and display it.

```php
<?php
// Step 1: Include config.php file

// Step 2: Check if the $_GET['id'] exists; if it does, get the product record from the database and store it in the associative array named $product.
// SQL example: SELECT * FROM products WHERE id = ?

// Step 3: If a product with that ID does not exist, display the message "A product with that ID did not exist."
?>
```

## Final Steps

- Implement CRUD functionality for customers, orders, and products similar to the `article.php`, `article_add.php`, `article_edit.php`, and `article_delete.php` files in the content management system example.
- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This simple e-commerce system provides essential functionalities for users to manage customers, orders, and products. By following these steps, you can create a basic platform for e-commerce activities, providing a foundation for further development and customization.
