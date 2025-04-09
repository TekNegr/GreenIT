# IT4GREEN Project Setup

## Prerequisites
- PHP 8.0+
- Composer
- Node.js 16+
- Git
- XAMPP (or equivalent LAMP stack)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/your-username/IT4GREEN.git
cd IT4GREEN
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create and configure .env file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in .env:
```env
DB_DATABASE=it4green
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

## Git Workflow

### Branching Strategy
- `main` - Production-ready code
- `develop` - Integration branch
- `feature/*` - Feature branches
- `hotfix/*` - Critical bug fixes

### Collaborating Safely
1. Always pull latest changes before starting work:
```bash
git pull origin develop
```

2. Create a new branch for your work:
```bash
git checkout -b feature/your-feature-name
```

3. Commit changes with descriptive messages:
```bash
git add .
git commit -m "Add: Implemented 3D map functionality"
```

4. Push your branch:
```bash
git push origin feature/your-feature-name
```

5. Create a Pull Request to `develop` branch for review

### Resolving Git Conflicts

#### When Push is Rejected
If you get "non-fast-forward" errors when pushing:
```bash
# First fetch latest changes
git fetch origin

# Then rebase your local branch
git rebase origin/main

# If conflicts occur:
# 1. Open conflicted files and resolve (look for <<<<<<< markers)
# 2. Mark as resolved:
git add .
git rebase --continue

# 3. Push your changes
git push origin main
```

#### Pulling Changes Correctly
Always specify the branch when pulling:
```bash
git pull origin main
```

#### Common Scenarios:
1. If you get "not specified a branch" error:
```bash
git pull origin main --rebase
```

2. If you accidentally committed to wrong branch:
```bash
git reset --soft HEAD~1  # Undo last commit keeping changes
git stash               # Save changes
git checkout correct-branch
git stash pop           # Apply changes
```

3. If you need to force push (use with caution!):
```bash
git push origin main --force
```

## Development Tips
- Run tests before pushing:
```bash
php artisan test
```
- Format your code:
```bash
npm run format
```
