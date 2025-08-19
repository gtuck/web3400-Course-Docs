# Assignment 00: Verify Your Development Environment & Workflow

Purpose: Confirm you can (1) create the course repository from the template, (2) add the instructor, (3) run the dev container locally or in a Codespace, (4) create / commit / push a file, (5) submit a correct URL.

## Learning Objectives
You can:
1. Generate a repo from a template (not a fork).
2. Add a collaborator by GitHub username.
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

## Step 1. Create Your Repository
1. Open the template: https://github.com/gtuck/web3400
2. Click: Use this template → Create a new repository.
3. Repository name (exact): `web3400-fall25`
4. Recommended visibility: Private (unless told otherwise).
5. After creation, the top should show: generated from gtuck/web3400  
   If it does NOT, delete the repo (Settings → Danger Zone) and redo. Do NOT click Fork.

## Step 2. Add Instructor as Collaborator
1. Repo → Settings → Collaborators & teams (Manage access).
2. Click: Add people (Invite a collaborator).
3. Enter GitHub username: `gtuck` (do not use an email).
4. Send invitation (status: pending until accepted).

## Step 3. Clone (Local Workflow) OR Use Codespace
Choose ONE:

A. Local:
- Click Code → Local → Open with GitHub Desktop (or copy HTTPS URL and `git clone`).
- Open the folder in VS Code.

B. Codespace:
- Click Code → Codespaces → Create codespace on `main`.
- First build may take several minutes.

## Step 4. Open / Build Dev Container (If Local)
- VS Code should prompt: Reopen in Container → Accept.
- Wait for build to finish (green remote indicator in lower-left).
- Optional check: Open terminal and run:
  ```bash
  php -v
  ```

## Step 5. Create `index.php`
Place at repository root (same level as README).

```php
<?php
echo "Hello from PHP";
```

Save the file.

## Step 6. Commit and Push
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

## (Optional) Run in Container
If a web server is included and a port is forwarded (e.g., 8080), open the forwarded URL. You should see: Hello from PHP

## Step 7. Submit
Submit this URL (replace YOUR-USER):

```
https://github.com/YOUR-USER/web3400-fall25/blob/main/index.php
```

Open it in an incognito/private window to ensure instructor visibility (collaborator invite must be accepted).

---

## Self-Checklist
- Repo name exactly `web3400-fall25`
- Shows “generated from gtuck/web3400”
- Instructor collaborator added (invite sent)
- `index.php` at repo root
- File outputs “Hello from PHP”
- File visible on `main` branch
- Submission URL correct

## Common Mistakes
- Forking instead of using the template.
- Misspelling `web3400-fall25`.
- Adding email instead of GitHub username.
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
Validate each checklist item; fix any
