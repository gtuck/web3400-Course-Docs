# Simple Ticketing System (Project 06)

In this project, you will develop a simple ticketing system that allows users to create, read, update, and delete tickets. The system will also include functionalities for adding ticket comments and user authentication, ensuring only logged-in users can manage tickets and comments. This structured approach will ensure the ticketing system functions correctly and provides a good user experience.

## Copy Project 05 to the Project 06 folder

- Recursively copy the project folder.
- Stage, commit, and push your new project to GitHub.

## Set Up the Database

Before coding, you must set up the database tables to store tickets and ticket comments. Use the following SQL statements to create the `tickets` and `ticket_comments` tables:

```sql
-- Table structure for table `tickets`
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Open','In Progress','Closed') NOT NULL DEFAULT 'Open',
  `priority` enum('Low','Medium','High') NOT NULL DEFAULT 'Low',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `tickets`
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

-- AUTO_INCREMENT for table `tickets`
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Table structure for table `ticket_comments`
CREATE TABLE `ticket_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `ticket_comments`
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

-- AUTO_INCREMENT for table `ticket_comments`
ALTER TABLE `ticket_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
```

## Create the PHP files

Create the following PHP files to handle various functionalities of the ticketing system:

- `tickets.php`: To display the list of tickets.
- `ticket-create.php`: To handle the creation of new tickets.
- `ticket-detail.php`: To display the details of a specific ticket, including comments.
- `ticket-edit.php`: To handle the editing of ticket details.
- `ticket-delete.php`: To handle the deletion of tickets.

## Create the `tickets.php` file

**HTML Structure**: Add the following HTML structure to your `tickets.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
	<h1 class="title">Manage Tickets</h1>
	<!-- Add Ticket Button -->
	<div class="buttons">
		<a href="ticket-create.php" class="button is-link">Create a new ticket</a>
	</div>
	<div class="row columns is-multiline">
		<?php foreach ($tickets as $ticket) : ?>
		<div class="column is-4">
			<div class="card">
				<header class="card-header">
					<p class="card-header-title">
						<?= htmlspecialchars(substr($ticket['title'], 0, 30), ENT_QUOTES) ?>
						&nbsp;
						<?php if ($ticket['priority'] == 'Low') : ?>
							<span class="tag"><?= $ticket['priority'] ?></span>
						<?php elseif ($ticket['priority'] == 'Medium') : ?>
							<span class="tag is-warning"><?= $ticket['priority'] ?></span>
						<?php elseif ($ticket['priority'] == 'High') : ?>
							<span class="tag is-danger"><?= $ticket['priority'] ?></span>
						<?php endif; ?>
					</p>
					<button class="card-header-icon">
						<a href="ticket-detail.php?id=<?= $ticket['id'] ?>">
							<span class="icon">
								<?php if ($ticket['status'] == 'Open') : ?>
									<i class="far fa-clock fa-2x"></i>
								<?php elseif ($ticket['status'] == 'In Progress') : ?>
									<i class="fas fa-check fa-2x"></i>
								<?php elseif ($ticket['status'] == 'Closed') : ?>
									<i class="fas fa-times fa-2x"></i>
								<?php endif; ?>
							</span>
						</a>
					</button>
				</header>
				<div class="card-content">
					<div class="content">
						<time datetime="2016-1-1">Created: <?= date('F dS, G:ia', strtotime($ticket['created_at'])) ?></time>
						<br>
						<p><?= htmlspecialchars(substr($ticket['description'], 0, 40), ENT_QUOTES) ?>...</p>
					</div>
				</div>
				<footer class="card-footer">
					<a href="ticket-detail.php?id=<?= $ticket['id'] ?>" class="card-footer-item">View</a>
					<a href="ticket-edit.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Edit</a>
					<a href="ticket-delete.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Delete</a>
				</footer>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `tickets.php` file. Your finished file displays the list of tickets.

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

// Prepared statement that retrieves all the tickets in descending order by creation date from the tickets table
?>
```

## Create the `ticket-create.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-create.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
   
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-create.php` file. Your finished file handles the creation of new tickets.

```php
<?php

?>
```

## Create the `ticket-detail.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-detail.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
   
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-detail.php` file. Your finished file displays the details of a specific ticket, including comments.

```php
<?php

?>
```

## Create the `ticket-edit.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-edit.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
   
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-edit.php` file. Your finished file handles the editing of ticket details.

```php
<?php

?>
```

## Create the `ticket-delete.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-delete.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
   
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-delete.php` file. Your finished file handles ticket deletion.

```php
<?php

?>
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This simple ticketing system provides essential functionalities for users to manage tickets and comments efficiently. By following these steps, you can create a platform for users to report and track issues, enhancing communication and issue resolution within your organization.
