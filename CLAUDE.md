# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 9** municipal/government services portal application named "Sahab". It's a bilingual (Arabic/English) platform for a municipal council that handles citizen services, complaints, suggestions, tenders, public sessions, and community initiatives.

**Key Technologies:**
- Laravel 9.x (PHP 8.0.2+)
- Laravel Passport (API authentication)
- Spatie Laravel Permission (role-based access control)
- Spatie Laravel Translatable (multilingual content)
- mcamara/laravel-localization (route localization)
- Vite (frontend build tool)
- MySQL database

## Development Commands

### Setup
```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed  # If seeders exist

# Storage link
php artisan storage:link
```

### Development
```bash
# Start development server
php artisan serve

# Frontend development
npm run dev

# Frontend build for production
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate optimized autoload files
composer dump-autoload
```

### Testing
```bash
# Run all tests
php artisan test
# or
./vendor/bin/phpunit

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run specific test method
php artisan test --filter test_method_name
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Format specific files
./vendor/bin/pint app/Http/Controllers
```

## Architecture Overview

### Multi-Guard Authentication System

The application uses **two separate authentication guards**:

1. **Admin Guard** (`auth:admin`):
   - Model: `App\Models\Admin`
   - Routes: `/admin/*` (routes/admin.php)
   - Login: `/admin/login`
   - Middleware: `auth:admin`

2. **User Guard** (default web guard):
   - Model: `App\Models\User`
   - Routes: Public routes (routes/web.php)
   - Authentication via modal/AJAX on frontend
   - Controllers: `AuthController::login()`, `AuthController::modalRegister()`

**Important**: Always verify which guard context you're working in when dealing with authentication.

### Route Structure

Routes are split across three files:

- **routes/web.php**: Public-facing frontend routes (citizen portal)
  - All routes wrapped in Laravel Localization middleware
  - Pattern: `/{locale}/...` (e.g., `/ar/about`, `/en/services`)

- **routes/admin.php**: Admin panel routes
  - Protected by `auth:admin` middleware
  - Prefix: `/admin/`
  - Uses resource controllers extensively
  - Role-based permissions via Spatie Permission package

- **routes/api.php**: Currently commented out/unused (legacy API routes)

### Controller Organization

**Frontend Controllers** (app/Http/Controllers/):
- Handle public-facing citizen interactions
- Examples: `ComplaintController`, `ServiceController`, `SuggestionController`
- Return views from `resources/views/user/`

**Admin Controllers** (app/Http/Controllers/Admin/):
- Handle CRUD operations for admin panel
- Heavily use resource controller pattern
- Return views from `resources/views/admin/`
- Examples: `ServiceController`, `ProjectController`, `TenderController`

### Localization Architecture

The app uses **mcamara/laravel-localization** for route-based locale switching:

- All routes are prefixed with locale: `/{locale}/route`
- Language files: `resources/lang/ar/` and `resources/lang/en/`
- Models use **Spatie Translatable** for database content translation
- Timezone: `Africa/Cairo`

When creating/editing translatable models, use the pattern:
```php
$model->setTranslation('field_name', 'ar', $arabicValue);
$model->setTranslation('field_name', 'en', $englishValue);
```

### Key Domain Models

**Citizen Engagement:**
- `Complaint` - Citizen complaints with tracking system
- `Suggestion` - Citizen suggestions
- `Contact` - Contact form submissions
- `ServiceForm` - Service request submissions

**Content Management:**
- `Service` + `ServiceDetail` - Municipal services (parent-child relationship)
- `Project` - Municipal projects
- `News` - News articles
- `Event` - Events
- `Banner` - Homepage banners
- `Page` - Static pages (Terms, Privacy Policy)

**Government Functions:**
- `Tender` + `TenderDetail` - Procurement tenders
- `PublicSession` - Public council sessions
- `NewListenSession` - Listening sessions
- `MunicipalCouncil` - Council member information
- `Law` - Legal documents

**Community:**
- `CommunityInitiatives` + `CommunityInitiativesUser` - Community initiatives with user support
- `TopicDiscussion` + `TopicDiscussionUser` - Discussion topics with user votes

### Helper Functions

Two auto-loaded helper files (defined in composer.json):

**app/Helpers/General.php:**
- `uploadImage($folder, $image)` - Upload images with unique filenames
- `uploadFile($file, $folder)` - Upload files

**app/Helpers/AppSetting.php:**
- `AppSetting::push_notification()` - FCM push notifications

### Views Structure

- `resources/views/admin/` - Admin panel views (Blade templates)
- `resources/views/user/` - Frontend citizen portal views
- `resources/views/layouts/` - Shared layout files

### Static Assets

- `assets/` - Admin panel assets (CSS, JS, images)
- `assets_front/` - Frontend assets (CSS, JS, images)
- `public/` - Publicly accessible files

### Database Conventions

- Migration files use year 2025 timestamps (project started in 2025)
- Models use soft deletes where applicable
- Tables follow Laravel plural naming convention
- Pivot tables for many-to-many: `{model1}_{model2}_table`

## Important Notes

### Pagination
Admin routes define a global constant: `PAGINATION_COUNT = 11`

### Role-Based Permissions
The admin panel uses Spatie Permission package with two guard types:
- Guard: `admin` for admin roles
- Guard: `web` for user roles

Check `routes/admin.php:73-75` for dynamic permission fetching by guard.

### Service Details Pattern
Services use a nested resource pattern:
- Parent: `Service` model
- Child: `ServiceDetail` model
- Routes: `/admin/services/{serviceId}/details/*`
- This pattern appears in `routes/admin.php:122-127`

### Facebook Integration
The app has Facebook SDK integration (`facebook/graph-sdk`) for displaying Facebook posts via `FacebookPost` model.

### Payment Integration
K-Net payment gateway integration via `asciisd/knet` package (Kuwait payment system).

### Newsletter
`NewsletterSubscription` model handles newsletter sign-ups via AJAX on frontend.

## Common Workflows

### Adding a New Admin Resource

1. Create migration: `php artisan make:migration create_table_name`
2. Create model: `php artisan make:model ModelName`
3. Create controller: `php artisan make:controller Admin/ModelNameController --resource`
4. Add route in `routes/admin.php`: `Route::resource('model-names', ModelNameController::class);`
5. Create views in `resources/views/admin/model-names/` (index, create, edit, show)
6. Add sidebar link in `resources/views/admin/includes/sidebar.blade.php`
7. Add translations in `resources/lang/ar/messages.php` and `resources/lang/en/messages.php`

### Adding Frontend Feature

1. Create/update controller in `app/Http/Controllers/`
2. Add routes in `routes/web.php` (inside localization group)
3. Create views in `resources/views/user/`
4. Add translations for any new text strings
5. Test in both Arabic and English locales

### Working with Translatable Content

Models using Spatie Translatable should:
- Use `HasTranslations` trait
- Define `$translatable` array property
- Access translated fields: `$model->getTranslation('field', 'ar')`
- Set translations: `$model->setTranslation('field', 'en', 'value')`