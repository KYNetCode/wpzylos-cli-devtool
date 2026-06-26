# WPZylos CLI Devtool

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-KYNetCode-181717?logo=github)](https://github.com/KYNetCode/wpzylos-cli-devtool)

Development CLI tool for scaffolding WPZylos plugins and generating boilerplate code.

📖 **[Full Documentation](https://wpzylos.com)** | 🐛 **[Report Issues](https://github.com/KYNetCode/wpzylos-cli-devtool/issues)**

---

## ✨ Features

- **42 Generator Commands** — Controllers, models, migrations, events, and 38 more
- **Smart Package Detection** — Warns if required packages are missing
- **Auto Context Resolution** — Reads namespace/slug from `.plugin-config.json`
- **Dry Run Mode** — Preview generated code before writing
- **App-Aware WordPress Generators** — Menu and shortcode stubs boot from `ApplicationInterface` and use WPZylos services

---

## 📋 Requirements

| Requirement     | Version        |
| --------------- | -------------- |
| PHP             | ^8.0           |
| Symfony Console | ^6.0 \|\| ^7.0 |

---

## 🚀 Installation

Install as a dev dependency in your plugin:

```bash
composer require --dev KYNetCode/wpzylos-cli-devtool
```

---

## 📖 Quick Start

After installing the package, you have **two options** to run CLI commands:

### Option 1: Use Vendor Binary Directly

Run commands directly from php vendor/bin:

```bash
php vendor/bin/wpzylos list
php vendor/bin/wpzylos make:controller ProductController
```

### Option 2: Create Root Executable (Recommended)

For a cleaner experience like Laravel's `php artisan`, run the installer once:

```bash
php vendor/bin/wpzylos-install
```

This creates a `wpzylos` file in your project root. Then use:

```bash
php wpzylos list
php wpzylos make:controller ProductController
php wpzylos make:request StoreProductRequest
php wpzylos make:migration create_products_table
```

---

## 🛠️ Available Commands (45+)

### Core Generators

| Command | Description | Output Path |
|---------|-------------|-------------|
| `make:plugin` | Scaffold a complete new plugin | Project root |
| `make:controller` | REST/HTTP controller | `app/Http/Controllers/` |
| `make:request` | FormRequest validation class | `app/Http/Requests/` |
| `make:migration` | Database migration | `database/migrations/` |
| `make:model` | Eloquent-style model | `app/Models/` |
| `make:provider` | Service provider | `app/Providers/` |
| `make:middleware` | HTTP middleware | `app/Http/Middleware/` |
| `make:service` | Service class | `app/Services/` |
| `make:config` | Configuration file | `config/` |
| `make:rest` | REST API controller | `app/Http/Controllers/` |
| `make:resource` | API resource transformer | `app/Http/Resources/` |

### WordPress-Specific

| Command | Description | Output Path |
|---------|-------------|-------------|
| `make:posttype` | Custom post type | `app/PostTypes/` |
| `make:taxonomy` | Custom taxonomy | `app/Taxonomies/` |
| `make:shortcode` | WPZylos/Vite shortcode with Gutenberg, Elementor, and WPBakery bridges | `app/WordPress/Shortcodes/` |
| `make:menu` | Admin menu page | `app/Admin/` |
| `make:settings` | Settings page | `app/Admin/` |
| `make:metabox` | Meta box | `app/Admin/` |
| `make:columns` | Admin list table columns | `app/Admin/` |
| `make:widget` | WordPress widget | `app/Widgets/` |
| `make:block` | Gutenberg block | `app/Blocks/` |

#### `make:menu`

Generates an admin menu class that boots from your service provider:

```php
MenuClass::boot($app);
```

Useful options:

```bash
php wpzylos make:menu ReportsMenu --icon-type dashicon --asset-handle reports-admin --vite-entry resources/js/admin.js --mount-id reports-admin
```

#### `make:shortcode`

Generates an app-aware shortcode with optional Gutenberg block, Elementor widget, WPBakery shortcode map, and Vite asset enqueue support.

Useful options:

```bash
php wpzylos make:shortcode FitCalculator --tag fit_calculator --title "Fit Calculator" --entry resources/js/app.js
```

### Events & Lifecycle

| Command | Description | Output Path |
|---------|-------------|-------------|
| `make:event` | Event class | `app/Events/` |
| `make:listener` | Event listener | `app/Listeners/` |
| `make:subscriber` | Event subscriber | `app/Listeners/` |
| `make:observer` | Model observer | `app/Observers/` |
| `make:action` | Action class | `app/Actions/` |
| `make:filter` | Filter class | `app/Filters/` |

### Jobs & Scheduling

| Command | Description | Output Path |
|---------|-------------|-------------|
| `make:job` | Queue job | `app/Jobs/` |
| `make:cron` | Cron/scheduled task | `app/Schedule/` |
| `make:schedule` | Schedule definition | `app/Schedule/` |

### Code Structure

| Command | Description | Output Path |
|---------|-------------|-------------|
| `make:ajax` | AJAX handler | `app/Http/Ajax/` |
| `make:asset` | Asset registration | `app/Assets/` |
| `make:cast` | Attribute cast | `app/Casts/` |
| `make:command` | CLI command | `app/Commands/` |
| `make:enum` | Enum class | `app/Enums/` |
| `make:exception` | Exception class | `app/Exceptions/` |
| `make:factory` | Model factory | `database/factories/` |
| `make:interface` | Interface/Contract | `app/Contracts/` |
| `make:mail` | Mailable class | `app/Mail/` |
| `make:notification` | Notification class | `app/Notifications/` |
| `make:policy` | Authorization policy | `app/Policies/` |
| `make:rule` | Validation rule | `app/Rules/` |
| `make:scope` | Query scope | `app/Scopes/` |
| `make:seeder` | Database seeder | `database/seeders/` |
| `make:test` | PHPUnit test | `tests/Unit/` |
| `make:trait` | Trait | `app/Traits/` |

---

## 🔧 Configuration

The CLI tool automatically detects your plugin's configuration from `.plugin-config.json`:

```json
{
  "plugin": {
    "namespace": "MyPlugin",
    "slug": "my-plugin",
    "prefix": "mp_",
    "textDomain": "my-plugin",
    "version": "1.0.0"
  }
}
```

---

## 🧪 Testing

```bash
# Run all tests
composer test

# Run with coverage
./php vendor/bin/phpunit --coverage-html coverage/
```

---

## 📦 Related Packages

| Package                                                                | Description                |
| ---------------------------------------------------------------------- | -------------------------- |
| [wpzylos-cli-core](https://github.com/KYNetCode/wpzylos-cli-core) | Stub compilation utilities |
| [wpzylos-wp-cli](https://github.com/KYNetCode/wpzylos-wp-cli)     | WP-CLI integration         |
| [wpzylos-scaffold](https://github.com/KYNetCode/wpzylos-scaffold) | Plugin template            |
| [wpzylos-core](https://github.com/KYNetCode/wpzylos-core)         | Application foundation     |

---

## 📖 Documentation

For comprehensive documentation, tutorials, and API reference, visit **[wpzylos.com](https://wpzylos.com)**.

---

## ☕ Support the Project

- [GitHub Sponsors](https://github.com/sponsors/KYNetCode)
- [PayPal Donate](https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC)

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [KYNetCode](https://github.com/KYNetCode)**
