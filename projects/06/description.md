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

-- Sample data for `tickets` table
INSERT INTO `tickets` (`user_id`, `title`, `description`, `status`, `priority`, `created_at`, `updated_at`) VALUES
(1, 'Login Issue', 'User unable to login to the system', 'Open', 'High', NOW(), NULL),
(1, 'Email Notification Failure', 'Email notifications are not being sent', 'In Progress', 'Medium', NOW(), NULL),
(1, 'Page Loading Error', 'Some pages are loading slowly or not at all', 'Open', 'Low', NOW(), NULL),
(1, 'Database Backup', 'Automated database backup failed last night', 'Closed', 'High', NOW(), NULL),
(1, 'User Interface Bug', 'Dropdown menu not displaying properly on mobile devices', 'Open', 'Medium', NOW(), NULL);

-- Sample data for `ticket_comments` table
INSERT INTO `ticket_comments` (`ticket_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 'User has tried resetting the password, the issue persists.', NOW()),
(1, 1, 'Issue escalated to the IT team for further investigation.', NOW()),
(2, 2, 'Confirmed that SMTP settings are correct.', NOW()),
(2, 3, 'Temporary workaround implemented, working on a permanent fix.', NOW()),
(3, 2, 'User reported issue with specific pages, need more details.', NOW()),
(3, 3, 'Optimizing database queries to improve page load times.', NOW()),
(4, 2, 'Backup process failed due to insufficient disk space.', NOW()),
(4, 3, 'Disk space issue resolved, backup process running smoothly now.', NOW()),
(5, 2, 'UI bug confirmed on several mobile devices.', NOW()),
(5, 3, 'CSS adjustments made, awaiting user confirmation.', NOW());
```

## Create the PHP files

Create the following PHP files to handle various functionalities of the ticketing system:

- `tickets.php`: To display the list of tickets.
- `ticket_create.php`: To handle the creation of new tickets.
- `ticket_detail.php`: To display the details of a specific ticket, including comments.
- `ticket_edit.php`: To handle the editing of ticket details.
- `ticket_delete.php`: To handle the deletion of tickets.

## Create the `tickets.php` file

**HTML Structure**: Add the following HTML structure to your `tickets.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Manage Tickets</h1>
    <!-- Add Ticket Button -->
    <div class="buttons">
        <a href="ticket_create.php" class="button is-link">Create a new ticket</a>
    </div>
    <div class="row columns is-multiline">
        <?php foreach ($tickets as $ticket) : ?>
            <div class="column is-4">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <?= htmlspecialchars_decode(substr($ticket['title'], 0, 30), ENT_QUOTES) ?>
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
                            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>">
                                <span class="icon">
                                    <?php if ($ticket['status'] == 'Open') : ?>
                                        <i class="far fa-clock fa-2x"></i>
                                    <?php elseif ($ticket['status'] == 'In Progress') : ?>
                                        <i class="fas fa-tasks fa-2x"></i>
                                    <?php elseif ($ticket['status'] == 'Closed') : ?>
                                        <i class="fas fa-times fa-2x"></i>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </button>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <time datetime="2016-1-1">Created: <?= time_ago($ticket['created_at']) ?></time>
                            <br>
                            <p><?= htmlspecialchars_decode(substr($ticket['description'], 0, 40), ENT_QUOTES) ?>...</p>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <a href="ticket_detail.php?id=<?= $ticket['id'] ?>" class="card-footer-item">View</a>
                        <a href="ticket_edit.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Edit</a>
                        <a href="ticket_delete.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Delete</a>
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

// Execute the query

// Fetch and store the results in the $tickets associative array

// Check if the query returned any rows. If not, display the message: "There are no tickets in the database."

?>
```

## Create the `ticket_create.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-create.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Create Ticket</h1>
    <form action="" method="post">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" placeholder="Ticket title" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Description</label>
            <div class="control">
                <textarea class="textarea" name="description" placeholder="Ticket description" required></textarea>
            </div>
        </div>
        <div class="field">
            <label class="label">Priority</label>
            <div class="control">
                <div class="select">
                    <select name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Create Ticket</button>
            </div>
            <div class="control">
                <a href="tickets.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-create.php` file. Your finished file handles the creation of new tickets.

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

// If the form was submitted, insert a new ticket into the database and redirect back to the `tickets.php` page with the message "The ticket was successfully added."

?>
```

## Create the `ticket_detail.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-detail.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Ticket Detail</h1>
    <p class="subtitle">
        <a href="tickets.php">View all tickets</a>
    </p>
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">
                <?= htmlspecialchars($ticket['title'], ENT_QUOTES) ?>
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
                <a href="ticket_detail.php?id=<?= $ticket['id'] ?>">
                    <span class="icon">
                        <?php if ($ticket['status'] == 'Open') : ?>
                            <i class="far fa-clock fa-2x"></i>
                        <?php elseif ($ticket['status'] == 'In Progress') : ?>
                            <i class="fas fa-tasks fa-2x"></i>
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
                <p><?= htmlspecialchars($ticket['description'], ENT_QUOTES) ?></p>
            </div>
        </div>
        <footer class="card-footer">
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=Closed" class="card-footer-item">
                <span class="icon"><i class="fas fa-times fa-2x"></i></span>
                <span>&nbsp;Close</span>
            </a>
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=In Progress" class="card-footer-item">
                <span><i class="fas fa-tasks fa_2x"></i></i></span>
                <span>&nbsp;In Progress</span>
            </a>
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=Open" class="card-footer-item">
                <span><i class="far fa-clock fa-2x"></i></span>
                <span>&nbsp;Re-Open</span>
            </a>
        </footer>
    </div>
    <hr>
    <div class="block">
        <form action="" method="post">
            <div class="field">
                <label class="label"></label>
                <div class="control">
                    <textarea name="msg" class="textarea" placeholder="Enter your comment here..." required></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Post Comment</button>
                </div>
            </div>
        </form>
        <hr>
        <div class="content">
            <h3 class="title is-4">Comments</h3>
            <?php foreach ($comments as $comment) : ?>
                <p class="box">
                    <span><i class="fas fa-comment"></i></span>
                    <?= date('F dS, G:ia', strtotime($comment['created_at'])) ?>
                    <br>
                    <?= nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES)) ?>
                    <br>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-detail.php` file. Your finished file displays the details of a specific ticket, including comments.

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

// Check if the $_GET['id'] exists; if it does, get the ticket record from the database and store it in the associative array named $ticket.

// Fetch comments for the ticket

// Update ticket status when the user clicks the status link

// Check if the comment form has been submitted. If true, then INSERT the ticket comment
?>
```

## Create the `ticket_edit.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-edit.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Ticket</h1>
    <form action="" method="post">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= htmlspecialchars_decode($ticket['title']) ?>" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Description</label>
            <div class="control">
                <textarea class="textarea" name="description" required><?= htmlspecialchars_decode($ticket['description']) ?></textarea>
            </div>
        </div>
        <div class="field">
            <label class="label">Priority</label>
            <div class="control">
                <div class="select">
                    <select name="priority">
                        <option value="Low" <?= ($ticket['priority'] == 'Low') ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= ($ticket['priority'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= ($ticket['priority'] == 'High') ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Ticket</button>
            </div>
            <div class="control">
                <a href="tickets.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-edit.php` file. Your finished file handles the editing of ticket details.

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

// Check if the update form was submitted. If so, UPDATE the ticket details.

// Else, it's an initial page request; fetch the ticket record from the database where the ticket = $_GET['id']
?>
```

## Create the `ticket_delete.php` file

**HTML Structure**: Add the following HTML structure to your `ticket-delete.php` file.

```html
<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete Ticket</h1>
    <p class="subtitle">Are you sure you want to delete ticket: <?= htmlspecialchars_decode($ticket['title']) ?></p>
    <div class="buttons">
        <a href="?id=<?= $ticket['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="tickets.php" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->
```

**PHP Processing**: Complete the following coding steps by adding your code to the top of the `ticket-delete.php` file. Your finished file handles ticket deletion.

```php
<?php
// Include config.php file

// Secure and only allow 'admin' users to access this page

// Check if the $_GET['id'] exists; if it does, get the ticket record from the database and store it in the associative array $ticket. If a ticket with that ID does not exist, display the message "A ticket with that ID did not exist."

// Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record.
// If yes, prepare and execute an SQL DELETE statement to remove the ticket where id == the $_GET['id'].
// Also, delete all comments associated with that ticket.
// Else (meaning they clicked 'no'), return them to the tickets.php page.
?>
```

## Final Steps

- Test your application thoroughly to catch and fix any bugs or issues.
- Ensure all files are correctly added and committed to your repository before pushing.
- Stage, commit, and push your final changes to GitHub.
- Submit your project URL as previously instructed, ensuring your GitHub repository is up to date so it can be accessed and evaluated.

## Conclusion

This simple ticketing system provides essential functionalities for users to manage tickets and comments efficiently. By following these steps, you can create a platform for users to report and track issues, enhancing communication and issue resolution within your organization.
