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

### Resolving Conflicts
1. If you encounter conflicts:
```bash
git fetch origin
git rebase origin/develop
```

2. Resolve conflicts in your editor
3. Continue rebase:
```bash
git add .
git rebase --continue
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
