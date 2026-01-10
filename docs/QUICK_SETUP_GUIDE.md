# Multi-Role Authentication System - Quick Setup Guide

## Phase 1 Implementation Complete ✅

All core authentication and role-based access control infrastructure has been implemented.

---

## Quick Start Steps

### 1. Run Database Migration

```bash
php artisan migrate
```

This will add:
- `role` column to `users` table
- `user_id` foreign keys to `tblinstructor` and `tblstudent` tables

### 2. Test the Login System

Open your browser and navigate to:
```
http://localhost:8000/login
```

### 3. Create Test Users

#### Option A: Use the Register Endpoint

**Register as Admin:**
```
http://localhost:8000/register/admin
- Name: Admin User
- Email: admin@example.com
- Password: password123
```

**Register as Faculty:**
```
http://localhost:8000/register/faculty
- Name: Faculty User
- Email: faculty@example.com
- Password: password123
```

**Register as Student:**
```
http://localhost:8000/register/student
- Name: Student User
- Email: student@example.com
- Password: password123
```

#### Option B: Use Database Seeder (Coming Soon)
A database seeder can be created to auto-populate test users.

### 4. Login and Test Dashboard

After registration, you'll be automatically logged in and redirected to your role's dashboard:
- **Admin:** `/admin/dashboard`
- **Faculty:** `/faculty/dashboard`
- **Student:** `/student/dashboard`

---

## System Architecture

### User Roles

| Role | Permissions | Dashboard |
|------|-------------|-----------|
| **Admin** | Full system access, manage all entities | `/admin/dashboard` |
| **Faculty** | Manage assigned sections and grades | `/faculty/dashboard` |
| **Student** | View enrollments, schedule, transcript | `/student/dashboard` |

### Authentication Flow

```
User → Login Page → Credentials Check → Role-Based Redirect → Dashboard
```

### Role-Based Access Control

```
Public Routes (No Auth Required)
├── /login
├── /register/{role?}
└── /

Protected Routes (Auth Required + Role Middleware)
├── /admin/* (requires admin role)
├── /faculty/* (requires faculty role)
└── /student/* (requires student role)
```

---

## File Overview

### Key Files Created/Modified

**Database:**
- ✅ `database/migrations/2026_01_07_000001_add_role_to_users_table.php`

**Controllers:**
- ✅ `app/Http/Controllers/AuthController.php` - Authentication logic
- ✅ `app/Http/Controllers/Admin/DashboardController.php`
- ✅ `app/Http/Controllers/Faculty/DashboardController.php`
- ✅ `app/Http/Controllers/Student/DashboardController.php`
- ✅ 8 additional Faculty controllers
- ✅ 7 additional Student controllers

**Middleware:**
- ✅ `app/Http/Middleware/IsAdmin.php`
- ✅ `app/Http/Middleware/IsFaculty.php`
- ✅ `app/Http/Middleware/IsStudent.php`

**Views:**
- ✅ `resources/views/auth/login.blade.php`
- ✅ `resources/views/auth/register.blade.php`
- ✅ `resources/views/layouts/app.blade.php` - Main layout
- ✅ `resources/views/layouts/base.blade.php` - Base template
- ✅ `resources/views/layouts/*-sidebar.blade.php` - Navigation menus
- ✅ `resources/views/admin/dashboard.blade.php`
- ✅ `resources/views/faculty/dashboard.blade.php`
- ✅ `resources/views/student/dashboard.blade.php`

**Configuration:**
- ✅ `bootstrap/app.php` - Middleware registration
- ✅ `routes/web.php` - Route organization
- ✅ `app/Models/User.php` - User model enhancements

---

## Features Implemented

### ✅ Authentication System
- Login form with validation
- Registration with role selection
- Password hashing and verification
- Session management
- Logout functionality
- Automatic role-based redirection

### ✅ Authorization System
- Role-based middleware
- Route protection by role
- Permission checking in controllers
- Unauthorized access handling

### ✅ User Interface
- Professional login/register pages
- Responsive design (Bootstrap 5)
- Role-specific navigation sidebars
- Dashboard layouts for each role
- Alert/notification system

### ✅ Routing Structure
- Public routes for authentication
- Protected admin routes
- Protected faculty routes
- Protected student routes
- RESTful naming conventions

---

## Next Steps (Phase 2)

### Admin Pages to Create
- [ ] User management (create, edit, delete users)
- [ ] Student management page
- [ ] Faculty/Instructor management page
- [ ] Course management page
- [ ] Department management page
- [ ] Program management page
- [ ] Section management page
- [ ] Enrollment management page

### Faculty Pages to Create
- [ ] Sections list/view pages
- [ ] Student roster page
- [ ] Grade entry and management page
- [ ] Schedule view page
- [ ] Reports generation page

### Student Pages to Create
- [ ] Enrollments list page
- [ ] Schedule view page
- [ ] Course catalog and details page
- [ ] Transcript page with GPA calculation
- [ ] Academic progress page
- [ ] Announcements page
- [ ] Profile edit page

---

## Testing Checklist

- [ ] Run migration successfully
- [ ] Navigate to `/login`
- [ ] Register as Admin user
- [ ] Register as Faculty user
- [ ] Register as Student user
- [ ] Login with each role
- [ ] Verify correct dashboard loads for each role
- [ ] Verify sidebar navigation matches role
- [ ] Test logout functionality
- [ ] Try accessing protected routes without auth (should redirect to login)
- [ ] Try accessing admin routes as student (should be denied)
- [ ] Try accessing faculty routes as admin (should be denied)

---

## Troubleshooting

### Migration Fails
```bash
# Check if columns already exist
php artisan migrate:refresh --seed
```

### Views Not Loading
```bash
# Clear view cache
php artisan view:clear
```

### Unauthorized Access Errors
- Check that your user has the correct role in database
- Verify middleware is registered in `bootstrap/app.php`
- Check route protection in `routes/web.php`

### Database Connection Issues
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## API Reference

### Authentication Routes

```php
// Public Routes
GET    /login                     - Show login form
POST   /login                     - Process login (AuthController@login)
GET    /register/{role?}          - Show register form (AuthController@showRegister)
POST   /register                  - Process register (AuthController@register)
POST   /logout                    - Logout (AuthController@logout)
```

### Admin Routes (Prefix: /admin, Middleware: auth,admin)

```php
GET    /admin/dashboard           - Admin dashboard
GET    /admin/students            - List students
GET    /admin/instructors         - List instructors
GET    /admin/courses             - List courses
GET    /admin/departments         - List departments
GET    /admin/programs            - List programs
GET    /admin/sections            - List sections
GET    /admin/enrollments         - List enrollments
GET    /admin/terms               - List terms
GET    /admin/rooms               - List rooms
```

### Faculty Routes (Prefix: /faculty, Middleware: auth,faculty)

```php
GET    /faculty/dashboard         - Faculty dashboard
GET    /faculty/sections          - My sections list
GET    /faculty/sections/{id}     - Section details
GET    /faculty/sections/{id}/students  - Student roster
GET    /faculty/sections/{id}/grades    - Grade management
GET    /faculty/schedule          - Class schedule
GET    /faculty/courses           - My courses
GET    /faculty/reports           - Reports
GET    /faculty/profile           - Profile view
PUT    /faculty/profile           - Profile update
```

### Student Routes (Prefix: /student, Middleware: auth,student)

```php
GET    /student/dashboard         - Student dashboard
GET    /student/enrollments       - My enrollments
GET    /student/schedule          - My schedule
GET    /student/courses           - Course catalog
GET    /student/courses/{id}      - Course details
GET    /student/transcript        - Transcript
GET    /student/progress          - Academic progress
GET    /student/announcements     - Announcements
GET    /student/profile           - Profile view
PUT    /student/profile           - Profile update
```

---

## Environment Variables

Ensure your `.env` file has proper configuration:

```env
APP_NAME=EnrollmentSystem
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:... (run php artisan key:generate)
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbenrollment
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
SESSION_DRIVER=database
```

---

## Support & Documentation

For detailed information, see:
- [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md) - Complete implementation details
- [ROLE_BASED_ACCESS_MATRIX.md](./ROLE_BASED_ACCESS_MATRIX.md) - Access control matrix

---

**Status:** Phase 1 ✅ Complete | Ready for Phase 2 Development
