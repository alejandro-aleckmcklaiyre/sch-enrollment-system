Laravel modules added: Departments and Enrollments

Packages to install:

- DOMPDF (for PDF exports)
  composer require barryvdh/laravel-dompdf

- Maatwebsite Excel (for Excel exports)
  composer require maatwebsite/excel

After installing packages, publish config if needed and clear caches:

php artisan vendor:publish --provider="Maatwebsite\\Excel\\ExcelServiceProvider" --tag=config
php artisan vendor:publish --provider="Barryvdh\\DomPDF\\ServiceProvider"
php artisan view:clear; php artisan route:clear; php artisan config:clear

Notes:
- Controllers: `DepartmentController`, `EnrollmentController` implemented with resource actions and export endpoints.
- Views: `resources/views/departments/*` and `resources/views/enrollments/*` created (index, partials/modals, export_pdf).
- Exports: `app/Exports/DepartmentExport.php`, `app/Exports/EnrollmentExport.php` use Maatwebsite Excel's FromCollection interface.
