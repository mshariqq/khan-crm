<div align="center">

# KhanCRM 🚀

**Open-Source Enterprise CRM & ERP System**

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-AGPLv3-blue?style=flat)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#️-tech-stack)
- [Requirements](#-requirements)
- [Quick Start](#-quick-start)
- [Screenshots](#-screenshots)
- [Module Deep Dive](#-module-deep-dive)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🌟 Overview

KhanCRM is a **production-ready, open-source CRM & ERP system** built for small to medium businesses. It combines **17 integrated modules** covering the full customer lifecycle — from lead capture to invoicing, project delivery, payroll, and support.

Whether you're managing clients, tracking projects, running a support desk, or processing payroll, KhanCRM provides a **unified dashboard** with **role-based access control**, a **client portal**, and a clean, modern UI.

---

## ✨ Features

<details>
<summary><strong>👥 Client Management</strong> — Full client CRM with portal access</summary>

- Complete client profiles with contacts, groups, and categories
- **Dedicated client portal** with login, project views, invoices, tickets, proposals & estimates
- CSV export, search, and advanced filtering
- Client-specific notes, reminders, and GDPR request management
</details>

<details>
<summary><strong>📋 Lead Management</strong> — Capture, track, and convert</summary>

- Lead sources & statuses with pipeline tracking
- **Web-to-lead forms** — capture leads directly from your website
- **Email integration** — auto-import leads from IMAP inboxes
- Lead scoring with value tracking
- One-click conversion to client
- Mark as lost/junk with activity logging
</details>

<details>
<summary><strong>📦 Project Management</strong> — Deliver projects on time</summary>

- Full project CRUD with multiple billing types (fixed, hourly, project rate)
- **Gantt chart view** for visual timeline planning
- **Milestones** with color coding and progress tracking
- Team discussions with threaded comments
- File uploads, notes, and timesheets
- Automatic progress calculation from tasks
</details>

<details>
<summary><strong>✅ Task Management</strong> — Kanban-powered productivity</summary>

- **Kanban board** with drag-and-drop status updates
- Task checklists with reusable templates
- **Built-in timer** — start/stop tracking per task
- Staff assignments, followers, and priorities
- Recurring tasks and billable tracking
- Quick-create modal and quick-view popups
</details>

<details>
<summary><strong>💰 Invoicing</strong> — Professional billing made simple</summary>

- Custom numbering, prefixes, and formats
- **Recurring invoices** with configurable cycles
- Partial payment tracking with multiple payment modes
- Credit notes with refund support
- PDF/print-friendly views
- Tax per item (dual tax supported)
- Invoice reminders and marking (paid/cancelled)
</details>

<details>
<summary><strong>📄 Estimates & Proposals</strong> — Close deals faster</summary>

- Custom numbering and pipeline ordering
- **Convert estimates to invoices** with one click
- **Digital signatures** for acceptance
- Public estimate request forms
- Proposal comments and send-via-email workflow
</details>

<details>
<summary><strong>🎫 Support Tickets</strong> — Customer support that scales</summary>

- Multi-department ticket system with priority levels
- **Email piping** — auto-import tickets from IMAP
- Predefined replies for quick responses
- Color-coded statuses and staff assignment
- Client portal ticket submission and reply
- Spam filtering and file attachments
</details>

<details>
<summary><strong>📚 Knowledge Base</strong> — Self-service for clients</summary>

- Public knowledge base with categorized articles
- Article groups with color coding
- Feedback system (helpful/not helpful)
- Staff-only articles for internal documentation
</details>

<details>
<summary><strong>👔 HR & Payroll</strong> — Complete workforce management</summary>

- **Attendance**: Clock in/out with IP logging, grace periods, auto-absent detection
- **Leave Management**: Leave types with quotas, approval workflows, company holidays
- **Payroll**: Auto-generate payslips, salary config (base + allowances + deductions), currency symbol settings
</details>

<details>
<summary><strong>📊 Expense Tracking</strong> — Keep an eye on spending</summary>

- Expense CRUD with categories
- Billable expenses linked to invoices
- Recurring expense support
- Tax tracking per expense
</details>

<details>
<summary><strong>🔐 Role-Based Access Control</strong> — Granular permissions</summary>

- Role management with per-feature permissions (view/create/edit/delete)
- Department-based staff organization
- **Two-factor authentication** support
- Session management and password policies
</details>

<details>
<summary><strong>🎨 Custom Branding</strong> — Make it yours</summary>

- Custom primary/secondary brand colors
- Company logo and favicon upload
- **Custom CSS** injection
- Login page background customization
- Configurable copyright footer text
</details>

<details>
<summary><strong>📧 Email Engine</strong> — Transactional emails with templates</summary>

- Fully configurable SMTP settings
- **Email templates** with placeholders for every module
- Email queue for reliable delivery
- Tracked mails with open-rate tracking
</details>

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.3, Laravel 13 |
| **Frontend** | Tailwind CSS 4, Alpine.js, Blade, Bootstrap Icons |
| **Build Tool** | Vite 8 |
| **Database** | SQLite (default) / MySQL / PostgreSQL / MariaDB |
| **Cache** | Database / Redis / Memcached |
| **Queue** | Database-driven job queue |
| **Auth** | Session-based with guards (staff + client) |
| **Assets** | Vite + Tailwind CSS v4 |

---

## 📋 Requirements

- PHP **8.3** or higher
- Composer **2.x**
- Node.js **20+** & NPM
- SQLite (built-in) or MySQL 8.0+ / PostgreSQL 16+

---

## 🚀 Quick Start

### 1. Clone & Install

```bash
git clone https://github.com/your-org/khncrm.git
cd khncrm

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install
```

### 2. Environment Setup

```bash
# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` to configure your database. The default uses SQLite (zero configuration):

```
DB_CONNECTION=sqlite
```

For MySQL, update accordingly:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database & Seed

```bash
# Run migrations to create all tables
php artisan migrate

# Seed default data (settings, roles, permissions, etc.)
php artisan db:seed
```

### 4. Build Frontend Assets

```bash
npm run build
```

### 5. Start Development Server

```bash
# All-in-one: server + queue + logs + Vite hot-reload
npm run dev
```

Or start components individually:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
npm run dev
```

Visit **http://localhost:8000** in your browser.

### 6. Quick Setup (All-In-One)

```bash
composer run setup
```

This runs `composer install`, creates `.env`, generates the app key, runs migrations, installs NPM deps, and builds assets.

---

## 🖼️ Screenshots

> *Screenshots coming soon — the project is under active development.*

| Dashboard | Projects | Invoicing |
|-----------|----------|-----------|
| *(coming)* | *(coming)* | *(coming)* |

| Kanban | Tickets | Payroll |
|--------|---------|---------|
| *(coming)* | *(coming)* | *(coming)* |

---

## 🔍 Module Deep Dive

### Architecture

KhanCRM follows Laravel's MVC architecture with a modular approach:

```
app/
├── Http/Controllers/
│   ├── Admin/        # 30+ controllers for admin area
│   ├── Client/       # 5 controllers for client portal
│   ├── Auth/         # Staff & client authentication
│   └── Guest/        # Public pages & knowledge base
├── Models/           # 110+ Eloquent models with relationships
├── Helpers/          # Global helpers & SettingsHelper
├── Services/         # Business logic (Email, File, etc.)
├── Providers/        # Service providers & view composers
└── Console/          # Artisan commands & scheduled tasks
```

### Database

- **110+ tables** covering all 17 modules
- Migrations with full schema definitions
- Seeders for default data (settings, roles, permissions, statuses)

### Key Design Decisions

| Decision | Rationale |
|----------|-----------|
| **Key-value settings** via `tbloptions` | Simple, flexible, no schema changes for new settings |
| **Cached settings** (24h TTL) | Minimize DB queries; cache cleared on update |
| **View composer** for global settings | Settings available in every Blade view as `$global_settings` |
| **Dual auth guards** | Separate `staff` and `client` authentication with independent sessions |
| **Polymorphic relationships** | Notes, reminders, tags, items, and custom fields attach to any module |
| **Database queue** | No external dependency (Redis/Beanstalkd) required out of the box |

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows the existing style and includes appropriate tests.

### Development Guidelines

- Follow [Laravel coding standards](https://laravel.com/docs/contributions)
- Use **Pint** for code style: `./vendor/bin/pint`
- Run tests before submitting: `composer run test`

---

## 📄 License

KhanCRM is open-sourced software licensed under the [AGPLv3 license](LICENSE). See `LICENSE` for the full terms.

---

<div align="center">
Made with ❤️ by the KhanCRM Team
</div>
