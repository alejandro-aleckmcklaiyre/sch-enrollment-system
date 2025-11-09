# Database Queries Documentation

This document shows the equivalent SQL queries that our Laravel Enrollment System generates. These queries demonstrate the relationship between tables and the core functionality of our system.

## File Locations for Query Implementations

1. Model Definitions:
- Enrollment queries: `app/Models/Enrollment.php`
- Student queries: `app/Models/Student.php`
- Section queries: `app/Models/Section.php`
- Course queries: `app/Models/Course.php`
- Instructor queries: `app/Models/Instructor.php`
- Program queries: `app/Models/Program.php`
- Department queries: `app/Models/Department.php`

2. Controller Implementations:
- Enrollment operations: `app/Http/Controllers/EnrollmentController.php`
- Student operations: `app/Http/Controllers/StudentController.php`
- Section operations: `app/Http/Controllers/SectionController.php`
- Course operations: `app/Http/Controllers/CourseController.php`

## Core Queries

### 1. Enrollment Listing with Student Information
**Location**: `app/Http/Controllers/EnrollmentController.php` (index method)
```sql
-- Equivalent to: Enrollment::with('student')->get();
SELECT 
    e.enrollment_id,
    e.date_enrolled,
    e.status,
    e.letter_grade,
    s.student_no,
    s.last_name,
    s.first_name
FROM tblenrollment e
JOIN tblstudent s ON e.student_id = s.student_id
WHERE e.is_deleted = 0 AND s.is_deleted = 0;
```

### 2. Section Details with Course and Instructor
**Location**: `app/Http/Controllers/SectionController.php` (index method)
```sql
-- Equivalent to: Section::with(['course', 'instructor'])->get();
SELECT 
    s.section_id,
    s.section_code,
    s.day_pattern,
    s.start_time,
    s.end_time,
    c.course_code,
    c.course_title,
    i.last_name as instructor_last_name,
    i.first_name as instructor_first_name
FROM tblsection s
JOIN tblcourse c ON s.course_id = c.course_id
JOIN tblinstructor i ON s.instructor_id = i.instructor_id
WHERE s.is_deleted = 0;
```

### 3. Student Search
**Location**: `app/Http/Controllers/StudentController.php` (index method)
```sql
-- Equivalent to our EnrollmentController search function
SELECT * FROM tblstudent
WHERE (last_name LIKE '%search_term%'
    OR first_name LIKE '%search_term%'
    OR student_no LIKE '%search_term%')
    AND is_deleted = 0;
```

### 4. Create New Enrollment
**Location**: `app/Http/Controllers/EnrollmentController.php` (store method)
```sql
-- Equivalent to: Enrollment::create($data);
INSERT INTO tblenrollment 
(student_id, section_id, date_enrolled, status, letter_grade, is_deleted)
VALUES 
(?, ?, CURRENT_DATE, 'Enrolled', NULL, 0);
```

### 5. Update Enrollment Status/Grade
**Location**: `app/Http/Controllers/EnrollmentController.php` (update method)
```sql
-- Equivalent to: $enrollment->update($data);
UPDATE tblenrollment 
SET status = ?,
    letter_grade = ?
WHERE enrollment_id = ?
    AND is_deleted = 0;
```

### 6. Student Program and Department Information
**Location**: `app/Http/Controllers/StudentController.php` (show method)
```sql
-- Equivalent to: Student::with(['program.department'])->get();
SELECT 
    s.student_id,
    s.student_no,
    s.last_name,
    s.first_name,
    p.program_code,
    p.program_name,
    d.dept_code,
    d.dept_name
FROM tblstudent s
JOIN tblprogram p ON s.program_id = p.program_id
JOIN tbldepartment d ON p.dept_id = d.dept_id
WHERE s.is_deleted = 0;
```

### 7. Section Enrollment Count
**Location**: `app/Http/Controllers/SectionController.php` (show method)
```sql
-- Shows available slots in sections
SELECT 
    s.section_id,
    s.section_code,
    c.course_code,
    s.max_capacity,
    COUNT(e.enrollment_id) as enrolled_count
FROM tblsection s
JOIN tblcourse c ON s.course_id = c.course_id
LEFT JOIN tblenrollment e ON s.section_id = e.section_id 
    AND e.is_deleted = 0
WHERE s.is_deleted = 0
GROUP BY s.section_id, s.section_code, c.course_code, s.max_capacity;
```

## Note
These SQL queries are the raw equivalent of what our Laravel application generates through its Eloquent ORM. The actual implementation uses Laravel's Query Builder and Model relationships for better security and maintainability.