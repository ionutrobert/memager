# Memager - Member & Loan Management System

A comprehensive Laravel-based admin panel for managing members, loans, payments, contact information, and workplace records. Built with **Filament** for an intuitive modern admin interface.

Perfect for microfinance institutions, credit unions, loan management companies, and membership organizations.

## ✨ Features

### Member Management
- Create, edit, and view detailed member profiles
- Track member join dates and history
- Store multiple contact information types (phone, email, address)
- Maintain workplace and employment details
- Search and filter members with advanced queries

### Loan Tracking & Accounting
- Create and manage multiple loans per member
- Track detailed payment history with automatic interest calculations
- Monitor outstanding debt and payment status
- Calculate current balance with interest accrual to today's date
- View comprehensive financial summaries per member

### Advanced Validation
- **CNP Validation**: Real-time validation of Romanian ID numbers with live feedback on member creation form
- Validates format and control digit using official CNP algorithm
- Prevents invalid CNP entries from being saved

### Dashboard & Analytics
- Admin dashboard with role-based access control
- Member management interface with sortable, filterable, hideable columns
- Comprehensive member detail view with all related information
- Payment history graphs with calculated debt summary

### Data Management
- Bulk import/export functionality via Excel (using Laravel Excel)
- Demo seeder with 5 realistic sample members and related data
- Database migration system for version control

## 🚀 Quick Start

### Prerequisites

- **PHP** 8.3 or higher
- **Composer** (latest version)
- **SQLite** (included in PHP, or switch to MySQL/PostgreSQL in `.env`)
- Optional: **Node.js** 16+ (if modifying frontend assets)

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/yourusername/memager.git
cd memager
```

**2. Install dependencies**
```bash
composer install
```

**3. Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Create database and seed demo data**
```bash
php artisan migrate:fresh --seed
```

This will:
- Create the SQLite database (at `database/database.sqlite`)
- Run all migrations to set up the schema
- Seed the database with demo data (admin user + 5 sample members with loans and payments)

**5. Start the development server**
```bash
php artisan serve
```

Open your browser and visit: **`http://localhost:8000`**

### 🔐 Default Login Credentials

After seeding, use these credentials to access the admin panel:

- **Email**: `admin@admin.com`
- **Password**: `password`

> ⚠️ **Important**: Change these credentials in production before deploying!

## 📋 Project Structure

```
memager/
├── app/
│   ├── Filament/                # Filament admin resource definitions
│   │   └── Resources/           # Member, Debt, Payment, Contact resources
│   ├── Http/
│   │   └── Controllers/         # Application controllers (e.g., CnpCheckController)
│   ├── Livewire/               # Livewire components (e.g., ViewImprumut for debt graphs)
│   ├── Models/                 # Eloquent models
│   ├── Rules/                  # Custom validation rules (e.g., CNP validator)
│   └── Providers/              # Service providers
├── database/
│   ├── migrations/             # Schema definitions
│   ├── seeders/                # Data seeders (DemoSeeder)
│   └── factories/              # Model factories for seeding
├── resources/
│   ├── views/                  # Blade templates
│   ├── js/                     # JavaScript (CNP validation client-side)
│   └── css/                    # Stylesheets
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes (if applicable)
├── config/                    # Configuration files
├── tests/                     # Test files
├── .env.example              # Environment template
└── composer.json             # PHP dependencies
```

## 🛠️ Core Technologies

| Technology | Purpose |
|-----------|---------|
| **[Laravel 10](https://laravel.com)** | Web framework |
| **[Filament v3](https://filamentphp.com)** | Admin panel & UI components |
| **[Livewire](https://livewire.laravel.com)** | Interactive components (graphs, real-time features) |
| **[Laravel Excel](https://laravel-excel.com)** | Excel import/export |
| **[Eloquent ORM](https://laravel.com/docs/eloquent)** | Database abstraction |
| **SQLite** | Local database (dev) |

## 📦 Key Dependencies

- `filament/filament` — Admin panel framework
- `livewire/livewire` — Real-time components
- `maatwebsite/excel` — Excel handling
- `spatie/laravel-permission` — Role/permission management
- `laravel/sanctum` — API authentication

See `composer.json` for complete list.

## 🗄️ Database Models

### Core Models
- **User** — Admin users with roles/permissions
- **Member** — Member profiles with contact and workplace info
- **Debt** (Imprumut) — Loan records
- **Payment** (Ramas) — Payment transactions
- **ContactInfo** — Phone, email, address for members
- **Workplace** — Employment information
- **DiscutieTelefonica** — Phone call logs
- **Nota** — Notes/records

### Relationships
- One Member → Many Debts (loans)
- One Member → Many Payments
- One Debt → Many Payments
- One Member → Many ContactInfo
- One Member → Has Latest Workplace

## 🔧 Configuration

### Environment Variables

Key variables in `.env`:

```env
APP_NAME=Memager
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (defaults to SQLite)
DB_CONNECTION=sqlite

# Mail (optional, for notifications)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Filament
FILAMENT_AUTHENTICATION_GUARD=web
```

For production, ensure:
- `APP_DEBUG=false`
- `APP_ENV=production`
- `APP_KEY` is securely set
- Database is production-grade (MySQL/PostgreSQL)

### Database

- **Development**: SQLite (default, zero-config)
- **Production**: Recommended to use MySQL or PostgreSQL

To switch databases, update `DB_CONNECTION` in `.env` and adjust other DB_* variables.

## 📝 Usage Examples

### Accessing the Admin Panel

1. Navigate to `http://localhost:8000`
2. Log in with demo credentials
3. Use the sidebar to navigate to Members, Loans, Payments, etc.

### Adding a New Member

1. Go to **Members** → **Create**
2. Fill in basic info (name, CNP, etc.)
3. System validates CNP in real-time
4. Add contact info and workplace details
5. Save

### Creating a Loan

1. Go to **Debts** → **Create**
2. Select member
3. Enter loan amount, interest rate, term
4. Mark payment milestones
5. System automatically tracks payment status

### Viewing Member Summary

1. Go to **Members** → Click on a member name
2. View:
   - Contact information (latest phone, email, address)
   - Workplace details
   - Loan summary with current balances
   - Payment history with interest calculations
   - Current date debt snapshot

## 🧪 Testing

Run tests with:

```bash
php artisan test
```

Or with coverage:

```bash
php artisan test --coverage
```

## 🐛 Troubleshooting

### "Database not found" error

Ensure migrations have run:
```bash
php artisan migrate:fresh --seed
```

### Port 8000 already in use

Use a different port:
```bash
php artisan serve --port=8001
```

### Missing JavaScript assets

Rebuild if you modified front-end code:
```bash
npm install
npm run dev
```

### Permission errors on Linux/Mac

Ensure proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

## 🔐 Security Notes

- **Default credentials are for demo/development only**. Change them immediately in production.
- Never commit `.env` file (it's in `.gitignore`)
- Always set `APP_DEBUG=false` in production
- Keep Laravel and dependencies updated: `composer update`
- Use HTTPS in production
- Implement proper backup strategy for production database

## 📚 Learn More

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)

## 📄 License

This project is open source and available under the **MIT License**. See the LICENSE file for details.

## 👨‍💻 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes and commit (`git commit -m 'Add amazing feature'`)
4. Push to your branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

For issues, questions, or suggestions, please open a GitHub issue.

---

**Last Updated**: February 2026  
**Laravel Version**: 10.48+  
**PHP Version**: 8.3+

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
