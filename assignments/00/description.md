## Verify your Development Environment and Workflow

Verify the development environment and workflow that will be used in the course. Topics should include Forking the web3400 GitHub repo, adding the instructor as a collaborator to your forked repo, Cloning the repo to your local computer and running the .devcontainer, or running it as a GitHub Codespace. Open your repository in Visual Studio Code, open the index.php file, and add the comment "//Function to display files in the website root folder as a link tree" on line 2. Then add, commit, and push the change to your forked web3400 repo.

**Please Note:** You may have completed some of these steps in a previous assignment. It's not meant for you to repeat the steps but to verify that you have completed all the steps in creating your dev environment and understand how to push changes and updates back to your repo so I can access them for grading.

### 1. Forking the web3400 GitHub Repository
- **Navigate to the Repository**: Open your web browser and go to [https://github.com/gtuck/web3400.git](https://github.com/gtuck/web3400.git).
- **Fork the Repository**: Click on the "Fork" button at the top right of the page to create a copy of the repository in your GitHub account.

### 2. Adding the Instructor as a Collaborator
- **Access Your Forked Repository**: Visit your GitHub account and open the forked `web3400` repository.
- **Go to Settings**: Click on your repository's "Settings" tab.
- **Invite Collaborator**: Select "Manage access" from the sidebar, then "Invite a collaborator", and enter your instructor's GitHub username (gtuck@weber.edu). Send the invitation.

### 3. Cloning the Repository
- **Open Terminal/Command Prompt**: Open a terminal or command prompt depending on your OS.
- **Clone the Repository**: Type `git clone [URL-of-your-forked-repo]`. Replace `[URL-of-your-forked-repo]` with the actual URL of your forked repository.

### 4. Running the .devcontainer locally or in a GitHub Codespace
- **For your local computer**:
  - Ensure Docker Desktop is installed and running.
  - Open the cloned repo in Visual Studio Code.
  - Reopen in a container if prompted by VS Code.
  
- **For GitHub Codespace**:
  - Go to your forked repository on GitHub.
  - Click "Code" and select "Open with Codespaces", then create a new codespace.
  - **Note**: There may be costs associated with using GitHub Codespaces.

### 5. Modifying `index.php` File
- **Open the Repository in Visual Studio Code**.
- **Find `index.php`**: Locate and open the `index.php` file.
- **Add the Comment**: On line 2, insert `// Function to display files in the website root folder as a link tree`.
- **Save the File**: Press `Ctrl + S` (Windows/Linux) or `Cmd + S` (Mac).

### 6. Adding, Committing, and Pushing the Changes
- **Open Terminal in VS Code**: Use Terminal -> New Terminal from the top menu.
- **Stage the Change**: Execute `git add index.php`.
- **Commit the Change**: Type `git commit -m "Add comment to index.php"`.
- **Push the Change**: Run `git push origin main`.

### 7. Submitting the Assignment
- **Confirm Changes on GitHub**: Visit your forked `web3400` repository on GitHub.
- **Locate the Updated `index.php` File**: Ensure your changes are visible.
- **Submit the Assignment**: Provide the URL of your updated `index.php` file in the format: `https://github.com/[your-account-name]/web3400/blob/main/index.php`. Replace `[your-account-name]` with your GitHub username.
