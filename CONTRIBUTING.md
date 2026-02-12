# Contributing to Memager

Thank you for considering contributing to the Memager project! We welcome contributions from the community.

## Code of Conduct

Be respectful and constructive in all interactions. This project is committed to providing a welcoming and inclusive environment.

## How to Contribute

### Reporting Bugs

Before creating a bug report, please check the issue list to avoid duplicates.

When reporting a bug, include:
- Clear descriptive title
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Environment details (PHP version, OS, etc.)

### Suggesting Enhancements

Feature requests are welcome! Provide:
- Clear use case and benefit
- Examples of how it would work
- Possible implementation approaches (optional)

### Pull Requests

1. **Fork the repository** and create your branch from `main`
   ```bash
   git checkout -b feature/amazing-feature
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Make your changes**
   - Follow PSR-12 code standards
   - Add tests for new functionality
   - Update documentation if needed

4. **Test your changes**
   ```bash
   php artisan test
   ```
   
   Ensure all tests pass before submitting.

5. **Commit with clear messages**
   ```bash
   git commit -m "Add: Description of feature/fix"
   ```
   
   Use conventional commit format:
   - `Add:` for new features
   - `Fix:` for bug fixes
   - `Improve:` for improvements
   - `Docs:` for documentation
   - `Tests:` for test additions

6. **Push to your fork**
   ```bash
   git push origin feature/amazing-feature
   ```

7. **Open a Pull Request**
   - Reference any related issues
   - Describe the changes and why they're needed
   - Ensure CI passes

## Development Workflow

### Setup Development Environment

```bash
# Clone your fork
git clone https://github.com/yourusername/memager.git
cd memager

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create and seed database
php artisan migrate:fresh --seed

# Run development server
php artisan serve
```

### Useful Commands

```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SomeTest.php

# Run with coverage
php artisan test --coverage

# Format code (if using Laravel Pint)
./vendor/bin/pint

# Check for issues
php artisan tinker  # Interactive shell

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Code Standards

- Follow **PSR-12** PHP coding standard
- Use meaningful variable and function names
- Add comments for complex logic
- Keep methods focused and concise
- Write tests for new features

## Documentation

When adding features or making significant changes:
- Update relevant sections in `README.md`
- Add inline code comments for complex logic
- Document new model relationships
- Provide examples for new functionality

## Git Workflow Best Practices

- Keep commits atomic (one logical change per commit)
- Write clear, descriptive commit messages
- Rebase before pushing if needed
- Keep feature branches up to date with `main`
- Squash commits before merging if instructed

## Testing Guidelines

- Write tests for new features
- Include both happy path and edge cases
- Tests should be independent and repeatable
- Use descriptive test method names

```php
// Good test name
public function test_it_validates_cnp_format()
{
    // test code
}

// Avoid vague names
public function test_cnp()
{
    // test code
}
```

## Questions?

- Open a discussion or issue for questions
- Check existing issues/PRs before asking
- Be patient and respectful in communication

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for helping make Memager better! 🚀
