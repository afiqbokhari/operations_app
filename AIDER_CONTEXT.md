# AIAC Operations App - Project Context

## Stack
- **Backend:** Laravel 12, PHP 8.2, MariaDB 10.11
- **Frontend:** Blade, Tailwind CSS, Flowbite, Alpine.js
- **Server:** AlmaLinux 8.10 (dev), AlmaLinux 10 (prod)
- **Version Control:** Git (GitHub), develop branch for new features

## Architecture

### Directory Structure
app/
├── Http/Controllers/ # All controllers
├── Models/ # Eloquent models
├── Services/ # ActivityLogger, future services
├── Traits/ # LogsActivity trait
├── Console/Commands/ # ImportBookingsCommand, ImportEventsCommand
resources/views/
├── layouts/app.blade.php # Main layout with nav, dark mode, module toggle
├── bookings/ # Booking CRUD views
├── events/ # Event CRUD views
├── schedule/ # Daily, Weekly, Monthly views + print-week
├── rooms/ # Room management
├── features/ # Feature/equipment management
├── users/ # User & Role management
├── permissions/ # Role permissions UI
├── menus/ # Menu management
├── search/ # Search results page


### Key Patterns
- **Controllers:** Resource-style (index, create, store, edit, update, destroy)
- **Validation:** Form Request classes or inline validate()
- **Permissions:** RoleMiddleware checks permissions table per route group
- **Logging:** ActivityLogger service (app/Services/ActivityLogger.php)
- **Pivot changes:** Always compare old/new values before syncing, log via ActivityLogger
- **Models:** Use LogsActivity trait for automatic create/delete logging

### Database Conventions
- Table names: snake_case, plural (bookings, events, menus)
- Foreign keys: singular_table_id (room_id, booking_id)
- Enums: lowercase_with_underscores (half_am, half_pm, full_day)
- Timestamps: created_at, updated_at (Laravel default)
- Soft deletes: deleted_at where needed

### Frontend Conventions
- **Tailwind:** utility-first, dark mode via `dark:` prefix
- **Flowbite:** tables, modals, buttons follow Flowbite patterns
- **Alpine.js:** x-data, x-show, @click for interactivity
- **Mobile:** `hidden md:flex` pattern, hamburger menu
- **Dark mode:** localStorage persistence, class-based toggle
- **Fixed table columns:** `table-fixed` + `w-[%]` classes

### Routes
- Auth-protected via `Route::middleware('auth')`
- Role-protected via `Route::middleware('role:resource,auto')`
- Named routes: resource.action (bookings.index, bookings.store)
- API routes for search autocomplete in routes/api.php

### Menu System
- Database-driven via `menus` table
- Supports parent-child dropdowns (parent_id)
- Module column: 'bookings' or 'front_desk'
- Permission-based visibility

### Module Toggle
- Session-based: session('module', 'bookings')
- ModuleController@switch handles toggle
- Nav filters menus by module column

### Import Commands
- ImportBookingsCommand: imports hearings from CSV
- ImportEventsCommand: imports meetings/events from CSV
- Both use fgetcsv for multi-line field support
- Extract breakout rooms from [+BR] notation
- Log skipped rows to storage/logs/

### Key Models & Their Relationships
- **Booking:** belongsTo Room, belongsToMany Feature (pivot: booking_features)
- **Event:** belongsTo Room, belongsToMany Feature (pivot: event_features)
- **Room:** hasMany Booking, hasMany Event, belongsToMany Feature
- **Feature:** belongsToMany Room, belongsToMany Booking, belongsToMany Event
- **Menu:** self-referential (parent_id for dropdowns), belongsToMany Permission
- **User:** belongsToMany Role, hasMany ActivityLogs
- **Role:** belongsToMany User, belongsToMany Permission

## Current State
- 377 real bookings imported
- Events/meetings module operational
- Schedule: Daily, Weekly, Monthly views with tooltips
- PDF export for weekly schedule
- Role-based permissions with UI management
- Activity logs with ActivityLogger service
- Module toggle between Bookings and Front Desk
- Front Desk module: framework ready, features to build

## Common Tasks & Patterns

### Adding a New Resource
1. Create Model with migration
2. Add LogsActivity trait
3. Create Resource Controller
4. Add routes with role middleware
5. Create Blade views (index, create, edit, show)
6. Add menu entry with permissions
7. Run composer dump-autoload

### Modifying Pivot Relationships
1. Always fetch old pivot values before syncing
2. Use ActivityLogger to log changes
3. Compare arrays using array_diff_assoc
4. Log old vs new in description

### Import CSV Data
1. Use fgetcsv for robust parsing
2. Handle multi-line fields correctly
3. Extract +[BR] notation from field
4. Log each skipped row with reason
5. Use transactions for data integrity

## Branch Info
- `main` - production
- `develop` - active development (Front Desk module WIP)

## Testing & Debugging
- PHPUnit for backend tests
- Laravel Telescope for debugging (if installed)
- Storage/logs/laravel.log for application logs
- Import logs stored in storage/logs/import_*.log

## Deployment Notes
- Dev: AlmaLinux 8.10, PHP 8.2
- Prod: AlmaLinux 10, PHP 8.2
- Ensure extensions: pdo_mysql, mbstring, xml, curl, zip, gd
- Run migrations and seeders after deployment
- Clear config cache: php artisan config:cache
- Restart queue workers if using queues