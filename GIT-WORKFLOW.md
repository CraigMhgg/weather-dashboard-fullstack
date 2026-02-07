# Git Workflow Guide

## Branch Structure

Our project uses a multi-branch workflow to separate frontend and backend development:

### Main Branches

- **`main`** - Production-ready code, merges from feature branches
- **`backend-setup`** - Laravel backend and API development
- **`develop`** (optional) - Integration branch for testing before merging to main

## Current Branch Organization

### Backend Branch (`backend-setup`)

Contains all Laravel backend code:

- Laravel framework installation
- API controllers and routes
- Database migrations
- VS Code debugging configuration
- Backend documentation

**When to use:**

- Adding new API endpoints
- Updating Laravel dependencies
- Modifying backend configuration
- Backend bug fixes

### Frontend Branch (Future: `frontend-dev`)

Will contain Vue.js frontend code:

- Vue components
- Frontend routing
- State management
- UI/styling changes

## Workflow

### Working on Backend

```bash
# Switch to backend branch
git checkout backend-setup

# Make your changes
# ...

# Stage and commit
git add .
git commit -m "feat: Add new weather API endpoint"

# Push to remote
git push origin backend-setup
```

### Working on Frontend

```bash
# Create/switch to frontend branch
git checkout -b frontend-dev

# Make your changes
# ...

# Stage and commit
git add .
git commit -m "feat: Add weather detail component"

# Push to remote
git push origin frontend-dev
```

### Merging to Main

```bash
# Ensure your branch is up to date
git checkout backend-setup
git pull origin backend-setup

# Switch to main
git checkout main
git pull origin main

# Merge your feature branch
git merge backend-setup

# Resolve any conflicts if needed
# ...

# Push to main
git push origin main
```

## Commit Message Conventions

Follow conventional commits format:

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Code style changes (formatting, etc.)
- `refactor:` - Code refactoring
- `test:` - Adding or updating tests
- `chore:` - Maintenance tasks

**Examples:**

```bash
git commit -m "feat: Add user authentication API"
git commit -m "fix: Resolve CORS issue in weather endpoint"
git commit -m "docs: Update API documentation"
```

## Keeping Branches in Sync

### Update backend branch with main changes

```bash
git checkout backend-setup
git merge main
git push origin backend-setup
```

### Update main with backend changes

```bash
git checkout main
git merge backend-setup
git push origin main
```

## Branch Naming Conventions

- Feature branches: `feature/description`
- Bug fixes: `fix/description`
- Hotfixes: `hotfix/description`
- Experiments: `experiment/description`

**Examples:**

- `feature/user-authentication`
- `fix/cors-configuration`
- `hotfix/critical-api-bug`

## Useful Git Commands

```bash
# View all branches
git branch -a

# View current status
git status

# View commit history
git log --oneline --graph

# View changes
git diff

# Discard changes
git restore <file>

# Unstage files
git restore --staged <file>

# Delete a branch
git branch -d <branch-name>

# Force delete a branch
git branch -D <branch-name>
```

## Tips

1. **Commit often** - Make small, logical commits
2. **Pull before push** - Always pull latest changes before pushing
3. **Write clear messages** - Use descriptive commit messages
4. **Test before merging** - Ensure code works before merging to main
5. **Keep branches updated** - Regularly sync with main to avoid conflicts

## Current Project State

```
weather-dashboard-fullstack/
├── backend/          (backend-setup branch)
│   └── Laravel API, Controllers, Config
├── frontend/         (main branch - will move to frontend-dev)
│   └── Vue.js App, Components
└── .vscode/         (backend-setup branch)
    └── Debug configuration
```

## Next Steps

1. Create `frontend-dev` branch for frontend development
2. Merge desired features to `main`
3. Keep branches synchronized
4. Consider creating `develop` branch for integration testing
