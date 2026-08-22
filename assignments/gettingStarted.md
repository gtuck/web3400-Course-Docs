## Developer Environment Setup & Configuration (Pre-Assignment for A00)

This preparatory module ensures you can complete Assignment 00 (environment verification). You will install required tools, create or claim your course repository through the WEB 3400 CRSApps workflow, open it in a dev container (locally or Codespaces), make a small edit, and confirm everything runs.

### Outcomes
By the end you can:
1. Create or claim your WEB 3400 repository through CRSApps.
2. Confirm the repo appears in the `web3400-fall26` GitHub organization.
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

## Step 1. Create or Claim Your Course Repository (CRSApps)
1. Make sure you are signed in to GitHub.
   - If you do not yet have a GitHub account, create one first and use your `@mail.weber.edu` email address.
2. Open:
   - https://crsapps.netlify.app/gh?instructor=gt&course=WEB3400&task=CourseTemplate
3. Enter your GitHub username.
4. Enter the passcode exactly:
   - `MrC0der2shoe$`
5. Submit the form and wait for the repo-creation confirmation.
6. Open the course organization:
   - https://github.com/web3400-fall26
7. Open the repository CRSApps created for you.
8. Do NOT manually create a separate repo for this course. Do NOT fork. Do NOT rename the course repo after CRSApps creates it.

Reason: the course now provisions student repos through the `web3400-fall26` organization instead of the old template-in-your-own-account workflow.

---

## Step 2. Confirm the Repo You Will Use
1. On GitHub, verify your repo is inside the `web3400-fall26` organization.
2. Use that repo for all assignments and projects this semester.
3. If you already created a repo through this link earlier, reuse it instead of creating a duplicate.
4. If you do not see your repo after submitting the form, refresh GitHub and then contact the instructor if it still does not appear.

---

## Step 3. Choose Your Development Mode
Pick one (you can switch later):

A. Local Dev Container (requires Docker Desktop)
- Clone via GitHub Desktop (File → Clone repository → select your repo from the `web3400-fall26` organization).
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
- Repo created through the CRSApps link
- Repo visible in the `web3400-fall26` organization
- Can open container (local or Codespace)
- `php -v` works inside container
- A commit pushed to `main`
- Screenshot captured

---

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Container build hangs at "Downloading" | Confirm Docker Desktop is running (local), or delete and recreate the Codespace (cloud) |
| Docker Desktop won't start / machine can't run Docker | Use the cloud path instead: GitHub Codespaces (Step 3) needs only a browser |
| Wrong GitHub username entered in CRSApps | Re-run the CRSApps link with the correct GitHub username, then use the repo created for that account |
| Repo created manually instead of through CRSApps | Stop using the manual repo; use the repo created in the `web3400-fall26` organization |
| `git` or `php` commands not found | You're in the host terminal — open a terminal *inside* the container (or reinstall VS Code + Dev Containers extension) |
| "Port 8000 already in use" | Stop the other server (Ctrl+C in its terminal) or find and stop it: `pkill -f "php -S"`. Do not switch to port 8080 — it is reserved for phpMyAdmin |
| Codespace stopped/deleted unexpectedly | Free-tier Codespaces idle out; your pushed commits are safe — create a new Codespace from the repo |
| Changes not visible on GitHub | You committed but didn't push (`git push`), or edited on the host instead of in the container |

**What a working setup looks like:** VS Code shows "Dev Container" in the bottom-left corner, the terminal prompt is inside the container, `php -v` prints a PHP 8.x version, and `pwd` ends with the course repo name GitHub created for you.

<!-- TODO(instructor): drop in one screenshot of the working VS Code + container state here. -->

---

## Common Mistakes to Avoid
- Creating a personal repo manually instead of using the CRSApps link.
- Entering the wrong GitHub username in the CRSApps form.
- Renaming the repo after CRSApps creates it.
- Editing files but forgetting to push.
- Running PHP on host instead of inside container.

---

## Verification Commands (Reference)
```bash
pwd            # ensure you are inside your course repo
ls -a          # see .devcontainer, .git, README.md
php -v
git remote -v  # origin should point to github.com/web3400-fall26/...
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
