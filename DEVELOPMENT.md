# Development Guide

Quick reference for developers working with Memager.

## Prerequisites Installation

### Windows (using Laragon recommended)

```bash
# Laragon comes with PHP, Composer, and MySQL
# Just download from https://laragon.org/
```

### macOS

```bash
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP and Composer
brew install php composer
```

### Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install php php-sqlite3 php-mbstring php-xml composer
```

## Project Setup (Step-by-Step)

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/memager.git
cd memager

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate encryption key
php artisan key:generate

# 5. Create and seed database
php artisan migrate:fresh --seed

# 6. Start development server
php artisan serve
```

Access the app at `http://localhost:8000`

Login: `admin@admin.com` / `password`

## Project Structure Quick Tour

```
app/
├── Filament/Resources/          # Admin panel resources (create/update/view screens)
│   ├── MemberResource.php       # Member management
│   ├── DebtResource.php         # Loan/debt management
│   └── ...
├── Http/Controllers/
│   └── CnpCheckController.php   # AJAX API endpoint for CNP validation
├── Livewire/
│   └── ViewImprumut.php         # Component for debt graphs and calculations
├── Models/                      # Database models (Member, Debt, Payment, etc.)
├── Rules/
│   └── Cnp.php                  # Custom validation rule for CNP

database/
├── migrations/                  # Create/modify tables (source of truth for schema)
├── factories/                   # Generate fake data for testing
└── seeders/
    └── DemoSeeder.php           # Create realistic demo data

resources/
├── views/
│   └── filament/               # Custom Filament views (optional overrides)
│   └── livewire/               # Livewire component views
└── js/
    └── cnp-check.js            # Client-side CNP validation

routes/
├── web.php                      # Web routes (public & auth)
└── api.php                      # API routes

config/                         # Configuration files
├── database.php                 # Database settings
├── filament.php                 # Filament admin settings
└── ...

storage/                        # Runtime files (don't track in Git)
├── logs/                        # Application logs (check for errors)
└── framework/                   # Cache, sessions, etc.
```

## Common Development Tasks

### Add a New Admin Resource (CRUD Screen)

```bash
# Generate resource with model and migration
php artisan make:filament-resource Member --generate

# This creates:
# - MemberResource in app/Filament/Resources/
# - Form builder in MemberResource::form()
# - Table builder in MemberResource::table()
# - Infolist builder in MemberResource::infolist() (detail view)
```

Then customize the `form()`, `table()`, and `infolist()` methods to define your interface.

### Create a New Model and Migration

```bash
# Generate model with migration and factory
php artisan make:model Member -mf

# This creates:
# - Model at app/Models/Member.php
# - Migration at database/migrations/xxxx_create_members_table.php
# - Factory at database/factories/MemberFactory.php

# Edit migration to define columns
# Edit factory to define fake data generation
```

### Run Database Operations

```bash
# Fresh migrate and seed (local development only!)
php artisan migrate:fresh --seed

# Run migrations only (without seeding)
php artisan migrate

# Rollback last batch of migrations
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset
```

### Work with Livewire Components

```bash
# Create a new Livewire component
php artisan make:livewire ViewMember

# This creates:
# - Component class at app/Livewire/ViewMember.php
# - Blade view at resources/views/livewire/view-member.blade.php
```

Edit the component class to handle logic, then update the Blade view for UI.

### Test Changes

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MemberTest.php

# Run with code coverage
php artisan test --coverage

# Run single test method
php artisan test --filter=test_create_member
```

### Clear Caches

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Or use a shortcut
php artisan optimize:clear
```

### Access Database

```bash
# Laravel Tinker (interactive shell for testing)
php artisan tinker

# Inside tinker:
> $member = Member::first();
> $member->debts()->count();
> Member::where('cnp', '1234567890123')->first();
```

## Understanding the Code

### Models and Relationships

Key model relationships:

```php
// app/Models/Member.php
class Member extends Model {
    // One-to-many: A member has many debts (loans)
    public function debts() {
        return $this->hasMany(Debt::class);
    }
    
    // One-to-many: A member has many payments
    public function payments() {
        return $this->hasMany(Payment::class);
    }
    
    // One-to-many: A member has many contact info
    public function contactInfo() {
        return $this->hasMany(ContactInfo::class);
    }
}

// app/Models/Debt.php
class Debt extends Model {
    // Belongs to: A debt belongs to one member
    public function member() {
        return $this->belongsTo(Member::class);
    }
    
    // One-to-many: A debt has many payments
    public function payments() {
        return $this->hasMany(Payment::class, 'imprumut_id');
    }
}
```

### Custom Validation

The CNP validator is implemented as a custom rule:

```php
// app/Rules/Cnp.php
class Cnp implements Rule {
    public function passes($attribute, $value) {
        // Validates CNP format and checksum
        return $this->isValidCnp($value);
    }
}

// Usage in forms:
'cnp' => ['required', new Cnp()]
```

### Filament Resource Structure

```php
// app/Filament/Resources/MemberResource.php

class MemberResource extends Resource {
    protected static ?string $model = Member::class;
    
    // Define list view columns
    public static function table(Table $table): Table {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            // ...
        ]);
    }
    
    // Define create/edit form
    public static function form(Form $form): Form {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('cnp')->rules([new Cnp()]),
            // ...
        ]);
    }
    
    // Define detail view
    public static function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
            TextEntry::make('name'),
            // ...
        ]);
    }
}
```

## Debugging Tips

### View Controller/Job Output

```bash
# Tail the application log in real-time
tail -f storage/logs/laravel.log   # macOS/Linux
Get-Content storage/logs/laravel.log -Tail 50 -Wait  # PowerShell
```

### Use dd() for Debugging

```php
// In your code
dd($variable);  // Dump and die - stops execution and shows variable

// Or use dump() to continue
dump($variable);  // Show variable but continue execution
```

### Test Database Queries

```bash
# In Tinker
> DB::enableQueryLog();
> Member::where('name', 'John')->first();
> dd(DB::getQueryLog());
```

### Check Environment

```bash
php artisan about  # Shows system info and environment details
```

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/member-photo-upload

# Make changes and commit
git add .
git commit -m "Add: Allow members to upload profile photos"

# Keep branch up to date
git fetch origin
git rebase origin/main

# Push and create pull request
git push origin feature/member-photo-upload
```

## Performance Tips

- Use eager loading to avoid N+1 queries:
  ```php
  // Bad: causes many queries
  $members->each(fn($m) => $m->debts)
  
  // Good: loads all in 2 queries
  $members->load('debts')
  ```

- Use indexes on frequently queried columns (defined in migrations)
- Cache expensive queries or computed values
- Use database transactions for multi-step operations

## File Modifications Checklist

When modifying the project:

- [ ] Update tests to cover new functionality
- [ ] Update documentation (README, CONTRIBUTING, etc.)
- [ ] Follow PSR-12 code standards
- [ ] Run `php artisan test` and ensure all pass
- [ ] Test edge cases and error scenarios
- [ ] Check database logs for N+1 queries
- [ ] Update CHANGELOG.md with your changes

## Useful Resources

- [Laravel Docs](https://laravel.com/docs)
- [Filament Docs](https://filamentphp.com/docs)
- [Livewire Docs](https://livewire.laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

---

Happy coding! 🚀
