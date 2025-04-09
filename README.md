<<<<<<< HEAD
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
=======
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> c59c0370266d23f318214ed15174a7c7c08b050f
