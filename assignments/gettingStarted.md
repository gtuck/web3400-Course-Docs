## Developer Environment Setup & Configuration (Pre-Assignment for A00)

This preparatory module ensures you can complete Assignment 00 (environment verification). You will install required tools, create the course repository from the template (NOT a fork), open it in a dev container (locally or Codespaces), make a small edit, and confirm everything runs.

### Outcomes
By the end you can:
1. Create a private repository from the course template named exactly `web3400-spring26`.
2. Add the instructor as collaborator (GitHub username: `gtuck@weber.eduw`).
3. Launch the dev environment (local Dev Container OR GitHub Codespace).
4. Verify PHP runs inside the container.
5. Make, commit, and push a simple change.
6. Capture a screenshot of a running container + repo in VS Code.

### Time Estimate
30–60 minutes (first-time Docker users: up to 90 minutes for downloads/build).

### Prerequisites
- Reliable internet.
- Laptop (Mac/Windows/Linux) with at least 8 GB RAM (16 GB preferred for Docker).
- Disk space: 5+ GB free.
- Ability to install software (admin rights).

### Tool Installation Order (Local Path)
1. GitHub account (https://github.com/join) – use your `@mail.weber.edu` email.
2. GitHub Desktop (https://desktop.github.com/)
3. Visual Studio Code (https://code.visualstudio.com/)
4. Docker Desktop (https://www.docker.com/products/docker-desktop/)
5. VS Code Extensions:
   - Dev Containers (ms-vscode-remote.remote-containers)
   - (Optional) GitHub Pull Requests & Issues

If using ONLY GitHub Codespaces, local Docker Desktop is not required.

---

## Step 1. Create Your Repository (Template → New Repo)
1. Open: https://github.com/gtuck/web3400
2. Click: Use this template → Create a new repository.
3. Repository name (exact, lowercase): `web3400-spring26`
4. Visibility: Private (unless told otherwise).
5. Confirm the banner: “generated from gtuck/web3400”.
   - If missing OR you clicked Fork by mistake: delete (Settings → Danger Zone) and redo.
6. Do NOT fork. Do NOT rename later.

Reason: Assignment 00 rubric checks exact name + template lineage.

---

## Step 2. Add Instructor as Collaborator
1. Repo → Settings → Collaborators & teams (Manage access).
2. Add people → type: `gtuck@weber.eduw`
3. Send invitation. (Email address is NOT used here.)
4. Leave it pending; you can continue.

---

## Step 3. Choose Your Development Mode
Pick one (you can switch later):

A. Local Dev Container (requires Docker Desktop)
- Clone via GitHub Desktop (File → Clone repository → select `web3400-spring26`).
- Open in VS Code.
- When prompted: Reopen in Container → Accept.
- First build may take several minutes (images download).

B. GitHub Codespace (no local Docker)
- Repo page → Code → Codespaces → Create codespace on `main`.
- Wait for build to finish (status bar shows container ready).

---

## Step 4. Verify Environment Inside Container
Open an integrated terminal (VS Code):
```bash
php -v
git --version
```
You should see PHP version output (e.g., 8.x). If `php` not found:
- Ensure you are INSIDE the container (green >< icon / remote indicator bottom-left).
- Rebuild: Command Palette → Dev Containers: Rebuild Container.

---

## Step 5. Make a Minimal Change
Edit `README.md` (add a short line like “Environment initialized.”) OR create a placeholder file:
```bash
echo "Environment OK" > ENV_CHECK.txt
```

---

## Step 6. Commit & Push
```bash
git status
git add README.md ENV_CHECK.txt
git commit -m "Initial environment confirmation"
git push origin main
```
Confirm on GitHub in the browser that the changes appear on `main`.

---

## Step 7. (Preview of Assignment 00 Requirement)
Next assignment (A00) will have you add `index.php`:
```php
<?php
echo "Hello from PHP";
```
You may do it now OR wait for A00. If you add it now, still review A00 instructions later.

---

## Step 8. Capture Deliverable Screenshot
Include ALL in one visible VS Code window (or Codespace):
- Explorer showing repository root with:
  - `.devcontainer` folder
  - `README.md`
  - (Optional) `index.php` if added
- An integrated terminal showing `php -v` output.
- Status bar indicating Dev Container OR Codespace active.

Save as `.png` or `.jpg`.

---

## Submission (This Setup Module)
Submit the screenshot via Canvas (WSU Online) (only the image). No URL yet required here unless instructed. Assignment 00 will ask for a direct GitHub file URL.

---

## Quick Checklist Before Moving to Assignment 00
- Repo name: `web3400-spring26`
- Template lineage banner present
- Instructor invited (`gtuck@weber.edu`)
- Can open container (local or Codespace)
- `php -v` works inside container
- A commit pushed to `main`
- Screenshot captured

---

## Troubleshooting
Issue: Container build hangs at “Downloading”.
Fix: Check Docker Desktop running (local) OR retry Codespace (delete and recreate).

Issue: Wrong repo name.
Fix: Create a NEW correctly named repo from template; optionally copy over any files; delete the misnamed one.

Issue: Added instructor by email.
Fix: Remove incorrect invite; re-invite using username `gtuck@weber.edu`.

Issue: `git` commands not found.
Fix: Make sure you are inside the container terminal (NOT host) or reinstall VS Code + Dev Containers extension.

---

## Common Mistakes to Avoid
- Forking instead of using template (breaks rubric linkage).
- Capitalizing repo name (`Web3400-Spring26` ≠ `web3400-spring26`).
- Skipping collaborator step.
- Editing files but forgetting to push.
- Running PHP on host instead of inside container.

---

## Verification Commands (Reference)
```bash
pwd            # ensure path ends with web3400-spring26
ls -a          # see .devcontainer, .git, README.md
php -v
git remote -v  # origin should point to your GitHub repo
```

---

## Next: Move to Assignment 00
Once the above checklist is green, open `assignments/00/description.md` and complete A00 (adds `index.php` + URL submission).

---

## Academic Integrity
Setup activity: collaboration fine (peer help, Q&A). Do not share account passwords or tokens.

## Support
- Discussion board tag: [Setup]
- Office hours (see syllabus)
- Provide screenshots + terminal output when asking for help.
