# Role-Based Access Control Matrix

## System Roles & Access Levels

---

## **1. ADMIN DASHBOARD & PAGES**

**Route Prefix:** `/admin`

### Dashboard
- **Route:** `GET /admin/dashboard`
- **Features:**
  - System overview statistics (total users, students, courses, enrollments)
  - Quick action cards
  - Recent activity log
  - System health status

### User Management
- **Route:** `/admin/users`
- **Features:**
  - View all users (students, faculty, admins)
  - Create new user (assign role)
  - Edit user details
  - Delete user accounts
  - Assign/change user roles
  - Activate/deactivate accounts

### Student Management
- **Route:** `/admin/students`
- **Features:**
  - View all students
  - Create new student
  - Edit student information
  - Delete student records
  - View student enrollments
  - Export student list (Excel/PDF)
  - Backup/Restore student data

### Faculty/Instructor Management
- **Route:** `/admin/instructors`
- **Features:**
  - View all instructors
  - Create new instructor
  - Edit instructor details
  - Delete instructor records
  - Assign departments
  - View assigned sections
  - Export instructor list (Excel/PDF)
  - Backup/Restore instructor data

### Course Management
- **Route:** `/admin/courses`
- **Features:**
  - View all courses
  - Create new course
  - Edit course details
  - Delete courses
  - Manage course prerequisites
  - View course enrollments
  - Export course list (Excel/PDF)
  - Backup/Restore course data

### Department Management
- **Route:** `/admin/departments`
- **Features:**
  - View all departments
  - Create new department
  - Edit department information
  - Delete departments
  - View department courses
  - Export department list (Excel/PDF)
  - Backup/Restore department data

### Program Management
- **Route:** `/admin/programs`
- **Features:**
  - View all programs
  - Create new program
  - Edit program details
  - Delete programs
  - Assign courses to program
  - View program enrollments
  - Export program list (Excel/PDF)
  - Backup/Restore program data

### Room Management
- **Route:** `/admin/rooms`
- **Features:**
  - View all rooms
  - Create new room
  - Edit room details
  - Delete rooms
  - Assign capacity and features
  - View room schedules
  - Export room list (Excel/PDF)
  - Backup/Restore room data

### Term Management
- **Route:** `/admin/terms`
- **Features:**
  - View all terms
  - Create new academic term
  - Edit term details
  - Delete terms
  - Set active term
  - View term schedules
  - Export term list (Excel/PDF)
  - Backup/Restore term data

### Section Management
- **Route:** `/admin/sections`
- **Features:**
  - View all sections
  - Create new section
  - Edit section details
  - Delete sections
  - Assign instructor
  - Assign room and schedule
  - View enrollments
  - Export section list (Excel/PDF)
  - Backup/Restore section data

### Enrollment Management
- **Route:** `/admin/enrollments`
- **Features:**
  - View all enrollments
  - Create enrollment (manual)
  - Edit enrollment details
  - Delete enrollment records
  - Update enrollment status (ENROLLED/DROPPED/COMPLETED)
  - Assign grades (if needed)
  - View enrollment history
  - Export enrollment list (Excel/PDF)
  - Backup/Restore enrollment data

### Course Prerequisites Management
- **Route:** `/admin/course-prerequisites`
- **Features:**
  - View all prerequisites
  - Add prerequisite relationship
  - Remove prerequisite
  - View prerequisite chains
  - Export prerequisites list (Excel/PDF)
  - Backup/Restore prerequisites data

### Reports & Analytics
- **Route:** `/admin/reports`
- **Features:**
  - Enrollment statistics
  - Student performance reports
  - Faculty workload reports
  - Course popularity analysis
  - Room utilization reports
  - Time-based analytics
  - Generate and download reports

### System Settings
- **Route:** `/admin/settings`
- **Features:**
  - Application configuration
  - Academic calendar settings
  - Grading scale configuration
  - Email/notification settings
  - System backup schedule
  - User activity logging

### Activity Logs
- **Route:** `/admin/logs`
- **Features:**
  - View all system activity
  - Filter by user, action, date
  - Download activity reports
  - Clear old logs

---

## **2. FACULTY/INSTRUCTOR DASHBOARD & PAGES**

**Route Prefix:** `/faculty`

### Dashboard
- **Route:** `GET /faculty/dashboard`
- **Features:**
  - Overview of assigned sections
  - Total students count
  - Quick links to grade management
  - Recent student submissions
  - Upcoming classes
  - Academic term information

### My Sections
- **Route:** `/faculty/sections`
- **Features:**
  - List all sections assigned to this instructor
  - View section details (course, schedule, room)
  - View number of enrolled students
  - Quick access to student list per section
  - Quick access to grade entry

### Class Schedule
- **Route:** `/faculty/schedule`
- **Features:**
  - View personal class schedule
  - Filter by term
  - See room assignments
  - See student count per section

### Student List
- **Route:** `/faculty/sections/{section_id}/students`
- **Features:**
  - View all students in assigned section
  - View student details (ID, name, email, program)
  - Filter/search students
  - Export student roster (Excel/PDF)

### Grade Management
- **Route:** `/faculty/sections/{section_id}/grades`
- **Features:**
  - View grade entry form for section
  - Enter/update letter grades for students
  - Calculate grade statistics
  - View grade distribution
  - Submit final grades
  - View grade history/changes
  - Print gradebook

### Enrollments View
- **Route:** `/faculty/sections/{section_id}/enrollments`
- **Features:**
  - View all enrollments in assigned section
  - View enrollment status (ENROLLED/DROPPED/COMPLETED)
  - See student enrollment details
  - View class attendance status (if tracked)

### Course Information
- **Route:** `/faculty/courses`
- **Features:**
  - View course details for taught courses
  - View course prerequisites
  - View course objectives
  - View course materials/syllabus
  - View course units and hours

### Reports
- **Route:** `/faculty/reports`
- **Features:**
  - Generate class roster reports
  - Generate grade reports
  - Generate attendance reports
  - Export reports (Excel/PDF)

### Profile
- **Route:** `/faculty/profile`
- **Features:**
  - View and edit own profile
  - Change password
  - View department assignment
  - View contact information

---

## **3. STUDENT DASHBOARD & PAGES**

**Route Prefix:** `/student`

### Dashboard
- **Route:** `GET /student/dashboard`
- **Features:**
  - Overview of current enrollments
  - Current term information
  - GPA display
  - Credit hours completed/remaining
  - Quick links to important pages
  - Important announcements/notices

### My Enrollments
- **Route:** `/student/enrollments`
- **Features:**
  - View all enrolled sections (current and past terms)
  - View section details (course, instructor, schedule, room)
  - View enrollment status
  - View current grades (if available)
  - View course materials/syllabus
  - Drop course (if allowed within deadline)

### Course Schedule/My Schedule
- **Route:** `/student/schedule`
- **Features:**
  - View personal class schedule
  - Filter by term
  - See assigned rooms
  - See instructor information
  - See course codes and titles
  - Sync with calendar

### Course Catalog
- **Route:** `/student/courses`
- **Features:**
  - Browse available courses
  - Filter by department, program, term
  - View course details
  - View course prerequisites
  - View course objectives and description
  - Enroll in course (if allowed)

### My Transcript
- **Route:** `/student/transcript`
- **Features:**
  - View all completed and current courses
  - View grades per course
  - View letter grades
  - View GPA per term and cumulative GPA
  - View credit hours per course
  - Download transcript (PDF)

### Course Details
- **Route:** `/student/courses/{course_id}`
- **Features:**
  - View course code and title
  - View course description
  - View course objectives
  - View course prerequisites
  - View course units and hours
  - View instructor(s)
  - View offered sections

### Academic Progress
- **Route:** `/student/progress`
- **Features:**
  - View degree progress
  - View completed requirements
  - View remaining requirements
  - View prerequisite completion status
  - See warning about failed prerequisites

### Profile
- **Route:** `/student/profile`
- **Features:**
  - View own profile information
  - View student ID and program
  - Edit contact information
  - Change password
  - View enrollment history

### Notifications/Announcements
- **Route:** `/student/announcements`
- **Features:**
  - View system announcements
  - View department announcements
  - View course-specific announcements
  - Mark announcements as read

---

## **ACCESS CONTROL SUMMARY TABLE**

| Feature | Admin | Faculty | Student |
|---------|:-----:|:-------:|:-------:|
| **Dashboard** | ✓ | ✓ | ✓ |
| User Management | ✓ | ✗ | ✗ |
| Student Management | ✓ | ✗ | ✗ |
| Faculty Management | ✓ | ✗ | ✗ |
| Course Management | ✓ | ✗ | ✗ |
| Department Management | ✓ | ✗ | ✗ |
| Program Management | ✓ | ✗ | ✗ |
| Room Management | ✓ | ✗ | ✗ |
| Term Management | ✓ | ✗ | ✗ |
| Section Management | ✓ | View Only | ✗ |
| Enrollment Management | ✓ | View Only | View Own |
| Course Prerequisites | ✓ | View | View |
| **Reports & Analytics** | ✓ | ✓ Limited | ✗ |
| **Backup/Restore** | ✓ | ✗ | ✗ |
| **Settings** | ✓ | ✗ | ✗ |
| **Activity Logs** | ✓ | ✗ | ✗ |
| Grade Management | ✓ | ✓ Assigned | ✗ |
| My Schedule | ✓ | ✓ | ✓ |
| Student Roster | ✓ | ✓ Assigned | ✗ |
| Transcript/Grades | ✓ | ✓ Limited | ✓ Own |
| Course Catalog | ✓ | View | ✓ |
| Profile Management | ✓ | ✓ | ✓ |

---

## **NOTES**

1. **All authenticated users** have access to their respective dashboards
2. **View-only access** means users can see data but cannot create/edit/delete
3. **Assigned/Own** means limited to data they manage or own
4. Faculty can only access sections/enrollments they are assigned to
5. Students can only view their own enrollments, schedule, and transcript
6. Admin has full access to all features and can generate any reports
7. All actions should be logged for audit trail purposes

---

## **Implementation Priority**

**Phase 1 (MVP):**
- Admin Dashboard + User/Student/Course Management
- Faculty Dashboard + My Sections + Grade Management
- Student Dashboard + My Enrollments + Schedule + Transcript

**Phase 2:**
- Complete admin reporting and analytics
- Faculty course information and student roster exports
- Student course catalog and academic progress

**Phase 3:**
- Activity logging and audit trails
- Advanced filtering and search
- Backup/restore functionality
- Fine-tuned permission controls
