## Verify Your Development Environment and Workflow

Ensure your development environment and workflow are properly set up for this course. The steps include creating the `web3400-Spr2025` GitHub repository from the template, adding the instructor as a collaborator, cloning the repository to your local computer, and running the `.devcontainer` locally or as a GitHub Codespace. You will also modify the `index.php` file and push the changes to your forked repository.

**Note**: If you have completed some steps in a previous assignment, you do not need to repeat them. Instead, verify that all steps are complete and that you understand how to push changes to your repository so the instructor can access them for grading.

### 1. Creating Your `web3400-Spr2025` GitHub Repository
- **Navigate to the Template Repository**: Open [https://github.com/gtuck/web3400.git](https://github.com/gtuck/web3400.git).
- **Use the Template**: Click the "Use this template" button to create a copy in your GitHub account.
- **Verify the Template**: Ensure your new repository displays "generated from `gtuck/web3400`". If it doesn’t, repeat this step.

### 2. Adding the Instructor as a Collaborator
- **Access Your Repository**: Open your `web3400-Spr2025` repository on GitHub.
- **Go to Settings**: Click on the "Settings" tab.
- **Invite the Instructor**: Under "Manage access," click "Invite a collaborator," enter the instructor's GitHub username (`gtuck@weber.edu`), and send the invitation.

### 3. Cloning Your Repository
- **Clone Locally**: In your repository, click the "Code" button, select the "Local" tab, and use "Open with GitHub Desktop" to clone the repository to your computer.

### 4. Running the `.devcontainer` Locally or in a GitHub Codespace
#### For Local Development:
- Ensure Docker Desktop is installed and running.
- Open the cloned repository in Visual Studio Code.
- If prompted, reopen the project in a container.

#### For GitHub Codespace:
- Open your forked repository on GitHub.
- Click "Code" and choose "Open with Codespaces." Create a new Codespace if needed.
- **Note**: There may be costs associated with using GitHub Codespaces.

### 5. Modifying the `index.php` File
- Open the repository in Visual Studio Code.
- Create a new file named `index.php` (if it doesn’t exist).
- Add the following code:
  ```php
  // Function to display files in the website root folder as a link tree
  <?php echo "Hello from PHP"; ?>
  ```
- Save the file.

### 6. Adding, Committing, and Pushing the Changes
- Open the terminal in Visual Studio Code (`Terminal -> New Terminal`).
- Stage the changes:
  ```bash
  git add index.php
  ```
- Commit the changes:
  ```bash
  git commit -m "Add comment to index.php"
  ```
- Push the changes:
  ```bash
  git push origin main
  ```

### 7. Submitting the Assignment
- Verify the changes in your `web3400-Spr2025` repository on GitHub.
- Ensure the `index.php` file contains your updates.
- Submit the assignment by providing the URL to your updated `index.php` file. Use the format:
  ```
  https://github.com/[your-account-name]/web3400-Spr2025/blob/main/index.php
  ```
- Replace `[your-account-name]` with your GitHub username.
