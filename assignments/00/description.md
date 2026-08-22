# Assignment 00: Verify Your Development Environment & Workflow

**Estimated time:** 1–2 hours · **Points:** see Canvas · **Due:** see the course schedule in Canvas

Purpose: Confirm you can (1) create or claim the course repository through CRSApps, (2) open it locally or in a Codespace, (3) run the dev container, (4) create / commit / push a file, (5) submit a correct URL.

## Learning Objectives
You can:
1. Create or claim your course repo through the CRSApps workflow.
2. Verify the repo is inside the `web3400-fall26` GitHub organization.
3. Launch the provided `.devcontainer`.
4. Create, commit, and push `index.php`.
5. Verify the file on GitHub via a direct URL.

## Prerequisites
- GitHub account (logged in).
- Git installed (if working locally).
- Docker Desktop running (local container option).
- VS Code + Dev Containers extension (local option).

## Note About Previously Completed Steps
If you already did any step correctly, just verify it and continue. Do not duplicate repos.

---

## Step 1. Create or Claim Your Repository
1. Sign in to GitHub. If you do not already have an account, create one first with your `@mail.weber.edu` email address.
2. Open the WEB 3400 CRSApps link:
   - https://crsapps.netlify.app/gh?instructor=gt&course=WEB3400&task=CourseTemplate
3. Enter your GitHub username.
4. Enter the passcode exactly:
   - `MrC0der2shoe$`
5. Submit the form and wait for the repo-creation confirmation.
6. Open https://github.com/web3400-fall26 and confirm the repo created for you is there.
7. Use that repo for all class work. Do NOT create a separate personal repo, fork, or rename the course repo.

## Step 2. Clone (Local Workflow) OR Use Codespace
Choose ONE:

A. Local:
- Open the repo CRSApps created for you, then click Code → Local → Open with GitHub Desktop (or copy HTTPS URL and `git clone`).
- Open the folder in VS Code.

B. Codespace:
- Click Code → Codespaces → Create codespace on `main`.
- First build may take several minutes.

## Step 3. Open / Build Dev Container (If Local)
- VS Code should prompt: Reopen in Container → Accept.
- Wait for build to finish (green remote indicator in lower-left).
- Optional check: Open terminal and run:
  ```bash
  php -v
  ```

## Step 4. Create `index.php`
Place at repository root (same level as README).

```php
<?php
echo "Hello from PHP";
```

Save the file.

## Step 5. Commit and Push
In VS Code terminal (or GitHub Desktop):

```bash
git status
git add index.php
git commit -m "Add index.php greeting"
git push origin main
```

Verify no pending changes:

```bash
git status   # should show: nothing to commit
```

## Step 6. (Optional) Run in Container
If a web server is included and a port is forwarded (e.g., 8000), open the forwarded URL. You should see: Hello from PHP

## Step 7. Submit
Submit the direct GitHub file URL for `index.php`. Replace `YOUR-REPO` with the repo CRSApps created for you in the `web3400-fall26` organization:

```
https://github.com/web3400-fall26/YOUR-REPO/blob/main/index.php
```

If you are unsure, open `index.php` on GitHub and copy the browser URL from the address bar.

---

## Self-Checklist
- Repo created through the WEB 3400 CRSApps link
- Repo visible inside the `web3400-fall26` organization
- `index.php` at repo root
- File outputs “Hello from PHP”
- File visible on `main` branch
- Submission URL correct

## Common Mistakes
- Creating a personal repo manually instead of using CRSApps.
- Entering the wrong GitHub username in CRSApps.
- Renaming the repo after CRSApps creates it.
- Putting `index.php` inside a subfolder.
- Editing but not committing/pushing.
- Leaving PHP outside `<?php ... ?>` tags.

## Rubric (30 pts)
- Complete/Incomplete

Late policy: See syllabus.

## Support
Use course discussion board with tag [A00] or visit office hours.

## Academic Integrity
Collaboration on setup is allowed. Do not share passwords or tokens.

## If Reusing Prior Work
Validate each checklist item and fix any missing setup steps before moving on.
