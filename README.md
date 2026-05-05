# CSIT Job Finder & Skill Development Platform

<p align="center">
  <img src="./logo.png" alt="Naresuan University CSIT Logo" width="300">
</p>

A web platform for the Computer Science and Information Technology (CSIT) department of Naresuan University. It connects students with professors who post part-time jobs, research assistantships, and project opportunities — while also tracking skills, managing applications, and providing a structured review system for student performance.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [Directory Structure](#3-directory-structure)
4. [Database Schema](#4-database-schema)
5. [Installation & Setup](#5-installation--setup)
6. [User Roles & Access](#6-user-roles--access)
7. [Authentication](#7-authentication)
8. [Feature Guide](#8-feature-guide)
   - [Home Page / Job Board](#81-home-page--job-board--hometestphp)
   - [Registration](#82-registration--registerphp--register_processphp)
   - [Student Profile & Dashboard](#83-student-profile--dashboard--stufphp)
   - [Teacher Profile & Dashboard](#84-teacher-profile--dashboard--teacher_profilephp)
   - [Posting a Job](#85-posting-a-job--jobpost2php)
   - [Managing a Job Post](#86-managing-a-job-post--jobmanagephp)
   - [Applying for a Job](#87-applying-for-a-job--jobapplyphp)
   - [Viewing & Processing Applications](#88-viewing--processing-applications--viewapplyphp)
   - [Review System](#89-review-system)
   - [Notifications](#810-notifications--viewnotiphp)
   - [Reports](#811-reports)
9. [AJAX API Reference](#9-ajax-api-reference)
10. [File Upload Handling](#10-file-upload-handling)
11. [Test Credentials](#11-test-credentials)
12. [Common Issues & Troubleshooting](#12-common-issues--troubleshooting)

---

## 1. Project Overview

The platform serves four user roles — students, professors, admins, and executives — each with their own dashboard and permissions. The core workflow is:

1. A **professor** posts a job opportunity with required skills, dates, reward type, and a cover image.
2. **Students** browse and filter jobs on the home page, then apply by submitting their resume, GPA, and contact information.
3. The professor reviews applications, approves or rejects them, and sets a salary for accepted students.
4. After the job, the professor leaves a multi-category **review** for accepted students, which appears on the student's profile.
5. **Admins** manage user accounts, roles, and taxonomies via the separate admin panel (`csit-job-finder-admin`).
6. **Executives** view a high-level analytics dashboard.

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (procedural, server-side rendered) |
| Database | MySQL (database name: `ip`) |
| Frontend | HTML5, CSS3, Vanilla JavaScript (AJAX) |
| Web Server | Apache via XAMPP / WAMP / MAMP |
| Icons | Bootstrap Icons (CDN) |
| Fonts | Google Fonts — Roboto, Montserrat |

---

## 3. Directory Structure

```
csit-job-finder-main/
│
├── database.php                  # MySQL connection (include in all pages)
├── login.php                     # Login form and session creation
├── logout.php                    # Session destroy and redirect
├── register.php                  # Registration form (student & teacher)
├── register_process.php          # Registration form handler
│
├── hometest.php                  # Home page / job board
├── navbar.php                    # Navigation bar component (included in pages)
│
├── [Student pages]
├── stuf.php                      # Student profile & dashboard
├── jobapply.php                  # Job application form & handler
├── review_student.php            # Student review display (from student view)
├── reviewst.php                  # Alt review display
│
├── [Teacher pages]
├── teacher_profile.php           # Teacher dashboard (job list, notifications)
├── jobpost2.php                  # Create new job post
├── jobmanage.php                 # Edit existing job post
├── viewapply.php                 # View all applications for a job
├── viewapply2.php                # Alt applications view
├── profilestapplication.php      # View a student's full profile (teacher POV)
├── approve_application.php       # AJAX: approve or reject an application
├── approve_handler.php           # Additional approval logic
├── update_status.php             # AJAX: toggle job open / close
├── save_close_job.php            # AJAX: save job closure with reason
├── studentlist.php               # List of students for a job
├── viewclosejob.php              # View closed jobs
│
├── [Shared review pages]
├── review.php                    # Review view (linked from teacher side)
├── reviewapplication.php         # Reviews tied to a specific application
├── calculate_review.php          # Included helper — aggregates reviews
├── calculate_review_student.php  # Student-specific aggregation helper
│
├── [Job browsing]
├── view_all_jobs.php             # All jobs in a category (with sort/filter)
│
├── [AJAX endpoints]
├── get_subskill.php              # Returns subskills for a skill_id (JSON)
├── get_job_subcategories.php     # Returns subcategories for a category (JSON)
├── get_job_categories.php        # Returns all job categories (JSON)
├── get_reason.php                # Returns job-close reasons (JSON)
├── get_status.php                # Returns current job status (JSON)
├── get_close_details.php         # Returns close detail options (JSON)
├── delete_skill_subskill.php     # Remove a skill from a job post
├── save_skills.php               # Save student skills to profile
├── update_profile.php            # Update teacher phone / email (AJAX)
├── fetch_resume.php              # Serve resume file for download
│
├── [Notifications & Reports]
├── viewnoti.php                  # View teacher notifications
├── viewnoti_reports.php          # View report-related notifications
├── report_process.php            # Submit a job report (student action)
│
├── [Assets]
├── css/                          # Per-page and shared stylesheets
├── js/                           # Per-page and shared scripts
├── images/                       # Default job cover images (img1.jpg …)
├── profile/                      # Uploaded profile pictures
├── resumes/                      # Uploaded resume files
└── logo.png                      # CSIT / Naresuan University logo
```

---

## 4. Database Schema

### Core tables

#### `user`
Central authentication table. Every account has one row here.

| Column | Type | Description |
|---|---|---|
| `user_id` | varchar | Login ID (e.g. `65312121`, `CSIT0131`) |
| `password` | varchar | Plain-text password (no hashing in current version) |
| `role_id` | int | 1 = Executive, 2 = Admin, 3 = Teacher, 4 = Student |
| `role_status_id` | int | 1 = Active, 2 = Disabled |

#### `student`
| Column | Description |
|---|---|
| `student_id` | FK → `user.user_id` |
| `stu_name` | Full name |
| `stu_email` | Email address |
| `major_id` | FK → `major` |
| `year` | Year of study |
| `gender_id` | FK → `gender` |
| `profile` | Profile image filename (stored in `profile/`) |
| `graduation_year` | Expected graduation year |

#### `teacher`
| Column | Description |
|---|---|
| `teacher_id` | FK → `user.user_id` |
| `teach_name` | Full name |
| `teach_email` | Email address |
| `major_id` | FK → `major` |
| `teach_phone_number` | Phone number |
| `gender_id` | FK → `gender` |
| `profile` | Profile image filename |

#### `post_job`
| Column | Description |
|---|---|
| `post_job_id` | Primary key |
| `title` | Job title |
| `description` | Full job description |
| `reward_type_id` | FK → `reward_type` (1 = hourly, 2 = one-time, 3 = daily) |
| `time_and_wage` | Wage / rate amount |
| `job_category_id` | FK → `job_category` |
| `job_subcategory_id` | FK → `job_subcategory` |
| `teacher_id` | FK → `teacher.teacher_id` |
| `job_status_id` | 1 = Open, 2 = Closed, 3 = Deleted by admin |
| `number_student` | Number of students needed |
| `job_start` | Start date/time |
| `job_end` | End date/time |
| `image` | Cover image filename |
| `created_at` | Timestamp |

#### `job_application`
| Column | Description |
|---|---|
| `job_application_id` | Primary key |
| `student_id` | Applicant |
| `post_job_id` | Job applied for |
| `GPA` | Student GPA at time of application |
| `stu_phone_number` | Contact number |
| `resume` | Uploaded resume filename |
| `created_at` | Timestamp |

#### `accepted_application`
| Column | Description |
|---|---|
| `job_application_id` | FK → `job_application` |
| `post_job_id` | Job post |
| `student_id` | Student |
| `accept_status_id` | 1 = Approved, 2 = Rejected |
| `created_at` | Timestamp |

#### `accepted_student`
| Column | Description |
|---|---|
| `student_id` | Accepted student |
| `post_job_id` | Job post |
| `salary` | Agreed salary / rate |

#### `review`
| Column | Description |
|---|---|
| `review_id` | Primary key |
| `student_id` | Student being reviewed |
| `teacher_id` | Reviewing teacher |
| `post_job_id` | Associated job |
| `rating` | Numeric score |
| `comment` | Free-text feedback |
| `review_category_id` | FK → `review_category` |
| `created_at` | Timestamp |

### Taxonomy / lookup tables

| Table | Purpose |
|---|---|
| `skill` / `subskill` | Technical skill hierarchy (skill → subskill) |
| `hobby` / `subhobby` | Interest area hierarchy |
| `job_category` / `job_subcategory` | Job classification hierarchy |
| `reward_type` | Wage types — hourly, one-time, daily |
| `close_detail` | Selectable reasons for closing a job |
| `gender` | Gender options |
| `major` | Academic major options |
| `role` | Role names (EN and TH) |
| `role_status` | Account status labels |
| `review_category` | Review criteria (e.g. Teamwork, Technical Skill) |

### Relationship tables

| Table | Links |
|---|---|
| `post_job_skill` | Job posts ↔ required skills/subskills |
| `student_skill` | Students ↔ their skills/subskills |

### System tables

| Table | Purpose |
|---|---|
| `notification` | Stores notifications for users (applications, approvals, rejections, reports) |
| `report` | Student-submitted flags on job posts |
| `close_job` | Records of closed jobs with reason and detail text |

---

## 5. Installation & Setup

### Prerequisites

Download and install **XAMPP** (recommended): [https://www.apachefriends.org](https://www.apachefriends.org)

Start the **Apache** and **MySQL** modules from the XAMPP Control Panel before proceeding.

---

### Step 1 — Place project files

Copy the project folder into the XAMPP web root:

```
C:\xampp\htdocs\csit-job-finder-main\
```

> On macOS the web root is `/Applications/XAMPP/htdocs/`. On Linux it is typically `/opt/lampp/htdocs/`.

If you also use the admin panel, place it alongside:
```
C:\xampp\htdocs\
├── csit-job-finder-main\
└── csit-job-finder-admin\
```

---

### Step 2 — Create and import the database

1. Open `http://localhost/phpmyadmin` in your browser.
2. Click **New** in the left sidebar.
3. Enter `ip` as the database name, choose `utf8mb4_unicode_ci` collation, and click **Create**.
4. With the `ip` database selected, click the **Import** tab.
5. Click **Choose File**, select the provided `.sql` dump file (from the `sample-database` branch), and click **Go**.

All tables and sample data will be created automatically.

---

### Step 3 — Configure the database connection

Open `csit-job-finder-main/database.php` and verify the credentials:

```php
$servername = "localhost";
$username   = "root";    // default XAMPP username
$password   = "";        // default XAMPP has no root password
$dbname     = "ip";
```

Update `$password` if you have set a MySQL root password.

> The admin panel has its own `db_connect.php` — update that file separately if needed.

---

### Step 4 — Create writable upload directories

The application stores uploaded files in two directories. Make sure they exist inside `csit-job-finder-main/`:

```
profile/      ← profile picture uploads
resumes/      ← resume PDF/document uploads
```

If they do not exist, create them. On Linux/macOS also set write permissions:
```bash
chmod 755 profile/ resumes/
```

---

### Step 5 — Start and access the application

1. Open the XAMPP Control Panel and confirm Apache and MySQL show **Running**.
2. Open your browser and navigate to:

```
http://localhost/csit-job-finder-main/login.php
```

You will see the login page. Use the sample credentials from [Section 11](#11-test-credentials) to log in.

---

## 6. User Roles & Access

| Role | role_id | Dashboard after login | Capabilities |
|---|---|---|---|
| Executive | 1 | `excutive/maindash.html` | High-level analytics dashboard |
| Admin | 2 | `admin/manage_users.php` | Full user, role, and permission management |
| Teacher / Professor | 3 | `hometest.php` | Post jobs, review applications, write student reviews |
| Student | 4 | `hometest.php` | Browse jobs, apply, manage profile and skills |

All pages that require authentication check for `$_SESSION['user_id']`. If the session is missing the user is redirected to `login.php`.

---

## 7. Authentication

### Login — `login.php`

The login form collects a **User ID** and **Password** and submits via POST.

**Server-side steps:**

1. Query: `SELECT * FROM user WHERE user_id = ? AND password = ?` (prepared statement).
2. If no match → display error "ไม่พบบัญชีผู้ใช้งาน" (Account not found).
3. If match but `role_status_id ≠ 1` → display error "บัญชีของคุณถูกระงับ" (Account disabled).
4. On success, store in session:
   - `$_SESSION['user_id']` — the login ID
   - `$_SESSION['user_role']` — the role_id
   - `$_SESSION['name']` — display name fetched from `student` or `teacher` table
5. Redirect based on role:

| role_id | Redirect |
|---|---|
| 1 | `excutive/maindash.html` |
| 2 | `admin/manage_users.php` |
| 3 | `hometest.php` |
| 4 | `hometest.php` |

### Logout — `logout.php`

Calls `session_destroy()` and redirects to `hometest.php`.

### Session variables

| Variable | Contains |
|---|---|
| `$_SESSION['user_id']` | Login ID string |
| `$_SESSION['user_role']` | Integer role ID |
| `$_SESSION['name']` | Display name |
| `$_SESSION['error']` | Flash error message (cleared after display) |

---

## 8. Feature Guide

### 8.1 Home Page / Job Board — `hometest.php`

The main landing page after login. Shows all open job posts.

**What it displays:**
- Grid of job cards — each shows job title, category, reward type, and cover image.
- Navigation bar (`navbar.php`) with links to job categories and subcategories.
- Filtering by category or subcategory updates the listing.

**Navigation bar (`navbar.php`):**
- Dynamically fetches all `job_category` rows from the database.
- Clicking a category filters jobs to show only that category.
- Clicking a subcategory narrows further.

---

### 8.2 Registration — `register.php` + `register_process.php`

New users self-register from the registration page.

**Form fields — all roles:**
| Field | Description |
|---|---|
| User ID | The login identifier (student number or staff ID) |
| Password | Plain text |
| Role | Student or Teacher (dropdown) |
| Name | Full name |
| Email | Contact email |
| Gender | Dropdown from `gender` table |
| Major | Dropdown from `major` table |

**Additional fields — Teacher only:**
| Field | Description |
|---|---|
| Phone number | Contact phone |

**Additional fields — Student only:**
| Field | Description |
|---|---|
| Year | Current year of study |
| Graduation year | Expected graduation year |

**Processing (`register_process.php`):**
1. Inserts a row into `user` with `role_id` and `role_status_id = 1`.
2. Inserts a row into either `student` or `teacher` depending on the chosen role.
3. Redirects to `login.php` on success.

---

### 8.3 Student Profile & Dashboard — `stuf.php`

**URL:** `stuf.php` (uses `$_SESSION['user_id']` to identify the student)

The student's personal dashboard. Displays:

- **Profile section** — name, email, major, year, profile picture. Students can upload a new profile photo.
- **Skills section** — lists all skill + subskill combinations the student has added. Skills can be added or removed. Adding is done via a two-level dropdown (skill → subskill) that populates dynamically via AJAX (`get_subskill.php`).
- **Hobbies section** — lists the student's hobbies and subhobbies.
- **Applications section** — lists all jobs the student has applied for with status indicators:
  - Pending — application submitted, awaiting review
  - Approved — teacher has accepted the application
  - Rejected — teacher has rejected the application
- **Reviews section** — summary of all performance reviews received from professors, with ratings per category and overall average.

---

### 8.4 Teacher Profile & Dashboard — `teacher_profile.php`

The teacher's personal dashboard. Displays:

- **Profile section** — name, email, major, phone. Phone and email can be updated inline via AJAX (`update_profile.php`).
- **Job posts section** — all jobs posted by this teacher, each showing title, status (Open/Closed), and links to manage or view applications.
- **Notifications section** — unread notifications about new applications, approvals, and reports. Clicking a notification marks it read and navigates to the relevant page.

**Notification types shown:**
- New student application on a job post
- Application approved / rejected (echoed back to teacher context)
- Report submitted on a job post

---

### 8.5 Posting a Job — `jobpost2.php`

**Access:** Teachers only. Linked from `teacher_profile.php`.

**Form fields:**

| Field | Description |
|---|---|
| Title | Job title |
| Description | Full description (textarea) |
| Job Category | Dropdown from `job_category` table |
| Job Subcategory | Dynamically loaded via AJAX when category changes |
| Required Skills | Multi-entry: choose skill → choose subskill → click Add. Saves to `post_job_skill`. |
| Number of students | How many students the teacher wants to hire |
| Reward Type | Dropdown: Hourly / One-time / Daily rate |
| Wage / Rate | Numeric amount |
| Start date/time | Job start |
| End date/time | Job end |
| Cover image | Select from preset images (img1.jpg, img2.jpg …) |

**On submit:**
1. Inserts one row into `post_job` with `job_status_id = 1` (Open).
2. For each skill/subskill added, inserts a row into `post_job_skill`.
3. Redirects to `teacher_profile.php`.

**JavaScript (`jobpost.js`):**
- Handles the skill hierarchy UI: selecting a skill fires AJAX to `get_subskill.php?skill_id=X`, populates the subskill dropdown.
- Selecting a job category fires AJAX to `get_job_subcategories.php?category_id=X`.
- The Add Skill button appends the chosen pair to a visible list and to hidden form inputs.
- Remove buttons call `delete_skill_subskill.php` for already-saved entries.

---

### 8.6 Managing a Job Post — `jobmanage.php`

**URL:** `jobmanage.php?post_job_id={id}`

Allows the teacher to edit all fields of an existing job post and to open or close it.

**Editing:**
All fields from the job post form are pre-populated. The teacher edits and clicks **Done** — the form POSTs back to `jobmanage.php` and updates the `post_job` row.

**Closing a job:**
1. Click the status button (shown as **Open** when job is open).
2. A modal appears asking for a closure reason.
3. The teacher selects from the `close_detail` dropdown. If "อื่น ๆ" (Other) is selected, a free-text input appears.
4. On submit, `save_close_job.php` is called via AJAX:
   - Sets `job_status_id = 2` in `post_job`.
   - Inserts a row into `close_job` with `post_job_id`, `close_detail_id`, and the optional custom text.

**Re-opening a job:**
When `job_status_id = 2`, the button shows **Closed**. Clicking it calls `update_status.php` via AJAX, which sets `job_status_id = 1`.

---

### 8.7 Applying for a Job — `jobapply.php`

**URL:** `jobapply.php?id={post_job_id}`

**Access:** Students only.

The application form pre-fills the student's name, email, major, and year from the `student` table. The student provides:

| Field | Description |
|---|---|
| GPA | Current GPA (numeric) |
| Phone number | Contact number for this application |
| Resume | File upload (PDF or document) |

**On submit:**
1. The resume file is saved to the `resumes/` directory with a timestamped filename.
2. A row is inserted into `job_application`.
3. A notification row is inserted into `notification` for the teacher who owns the job:
   - `event_type` = application event
   - `message` = student name + job title
   - `reference_table` = `job_application`
   - `reference_id` = new application ID
4. The page redirects back (two steps) to where the student was browsing.

---

### 8.8 Viewing & Processing Applications — `viewapply.php`

**URL:** `viewapply.php?post_job_id={id}`

**Access:** Teachers only (job must belong to the logged-in teacher).

Displays a table of all applications for one job post.

**Columns:** Student name, Email, Major, Year, GPA, Resume (download link), Status, Actions.

**Filtering:**
- Dropdown to filter by major
- Dropdown to filter by year
Both filters are applied server-side on page reload.

**Viewing a resume:**
Clicking the resume link calls `fetch_resume.php?file={filename}`, which serves the file as a download. A fullscreen viewer (`fullscreenResume.js`) is also available inline.

**Viewing a student profile:**
Clicking a student's name opens `profilestapplication.php?student_id={id}&post_job_id={id}`, which shows the student's full profile, skills, and application history.

**Approving or rejecting:**
Each pending application has two buttons — **Approve** and **Reject**.

Clicking either sends an AJAX POST to `approve_application.php`:

| POST field | Value |
|---|---|
| `application_id` | The application ID |
| `post_job_id` | The job post ID |
| `student_id` | The student ID |
| `action` | `approve` or `reject` |
| `salary` | Required when action = `approve` |

**Server-side (`approve_application.php`):**
- Inserts a row into `accepted_application` with `accept_status_id = 1` (approve) or `2` (reject).
- If approved: inserts a row into `accepted_student` with the salary value.
- Inserts a notification for the student with the result.

**Response:** JSON `{ "success": true }` or `{ "success": false, "error": "..." }`.

The page updates the action buttons to reflect the new status without a full reload.

---

### 8.9 Review System

#### Writing a review (Teacher)

After a job is completed, the teacher can write a review for each accepted student. Reviews are submitted with:

| Field | Description |
|---|---|
| `student_id` | Student being reviewed |
| `teacher_id` | Reviewing teacher |
| `post_job_id` | Associated job |
| `rating` | Numeric score |
| `comment` | Free-text feedback |
| `review_category_id` | The category of the review (e.g. Teamwork, Technical Skill) |

Multiple review rows can exist for the same student + job combination (one per category).

#### Viewing reviews — `review_student.php` / `reviewst.php`

Displays reviews received by the logged-in student.

**What is shown:**
- Reviews grouped by teacher + job post
- Per-group: all category ratings and the teacher's comment
- Category averages across all reviews
- Overall average rating

**Aggregation logic (`calculate_review.php` / `calculate_review_student.php`):**
These files are included by the view pages. They:
1. Fetch all `review` rows for the student.
2. Group rows by `(teacher_id, post_job_id)` pairs.
3. Sum and average ratings per `review_category_id`.
4. Compute the overall mean across all reviews.

#### Teacher review view — `review.php`

Shows reviews that the logged-in teacher has written for a specific student, accessible from the application management flow.

---

### 8.10 Notifications — `viewnoti.php`

**Access:** Teachers. Linked from `teacher_profile.php`.

Lists all notifications for the logged-in teacher from the `notification` table. Each row shows:
- Message text
- Event type (application received, report, etc.)
- Timestamp
- Link to the relevant page (job post or application)

Clicking a notification marks its `status` as read and navigates to the referenced record.

`viewnoti_reports.php` shows the subset of notifications related to reports filed against the teacher's job posts.

---

### 8.11 Reports

Students can flag a job post as inappropriate or problematic.

**Submitting a report:** Handled by `report_process.php`. Inserts a row into the `report` table with `report_status_id = 1` (pending).

**Admin resolution:** Admins review pending reports in the admin panel (`reports.php`) and either delete the job post or dismiss the report. See the admin panel README for details.

---

## 9. AJAX API Reference

All endpoints accept GET or POST as described and return JSON unless noted.

| Endpoint | Method | Parameters | Returns |
|---|---|---|---|
| `get_subskill.php` | GET | `skill_id` | Array of `{ subskill_id, subskill_name }` |
| `get_job_subcategories.php` | GET | `category_id` | Array of `{ job_subcategory_id, job_subcategory_name }` |
| `get_job_categories.php` | GET | — | Array of `{ job_category_id, job_category_name }` |
| `get_reason.php` | GET | — | Array of close reason options |
| `get_status.php` | GET | `post_job_id` | `{ job_status_id }` |
| `get_close_details.php` | GET | — | Array of `{ close_detail_id, close_detail_name }` |
| `update_status.php` | POST | `post_job_id` | `{ success: true/false }` |
| `save_close_job.php` | POST | `post_job_id`, `close_detail_id`, `detail` | `{ success: true/false }` |
| `approve_application.php` | POST | `application_id`, `post_job_id`, `student_id`, `action`, `salary` | `{ success: true/false }` |
| `delete_skill_subskill.php` | POST | `post_job_id`, `skill_id`, `subskill_id` | `{ success: true/false }` |
| `save_skills.php` | POST | `student_id`, skill/subskill arrays | `{ success: true/false }` |
| `update_profile.php` | POST | `teacher_id`, `phone` or `email` | `{ success: true/false }` |
| `fetch_resume.php` | GET | `file` | File download (binary) |

---

## 10. File Upload Handling

### Profile pictures
- **Upload location:** `profile/`
- **Naming:** `profile_{user_id}.{ext}` (or similar timestamp-based naming)
- **Referenced in:** `student.profile` and `teacher.profile` columns

### Resumes
- **Upload location:** `resumes/`
- **Naming:** Timestamped prefix + original filename (e.g. `1716800000_cv.pdf`)
- **Referenced in:** `job_application.resume` column
- **Served via:** `fetch_resume.php?file={filename}` — reads the file from the `resumes/` directory and outputs it with appropriate headers for download

> Both directories must exist and be writable by the web server process. On XAMPP Windows this is automatic. On Linux run `chmod 755 profile/ resumes/`.

---

## 11. Test Credentials

| Role | User ID | Password |
|---|---|---|
| Student | `65312121` | `stu1234` |
| Teacher / Professor | `CSIT0131` | `teach1234` |
| Admin | `admin0141` | `admin1234` |
| Executive | `exec0146` | `exec1234` |

> These accounts must exist in the imported database. If you start from a fresh schema without sample data, register new accounts through `register.php` and manually set `role_status_id = 1` in the `user` table via phpMyAdmin.

> **Security note:** Passwords are stored as plain text in the current version. Do not deploy this application on a public server without adding password hashing (e.g. `password_hash()` / `password_verify()`).

---

## 12. Common Issues & Troubleshooting

### Blank page or "Connection failed" error
- Confirm Apache and MySQL are running in the XAMPP Control Panel.
- Check `database.php`: the `$password` field must match your MySQL root password (empty string `""` by default on XAMPP).
- Verify the database name is `ip` and the schema was imported successfully.

### Redirected to login immediately
- PHP sessions require the `session.save_path` directory to be writable.
- On XAMPP Windows this is `C:\xampp\tmp`. Confirm the folder exists.
- Clear browser cookies and try again.

### Profile pictures or resumes not displaying / uploading
- Confirm the `profile/` and `resumes/` directories exist inside `csit-job-finder-main/`.
- On Linux/macOS, verify write permissions: `chmod 755 profile/ resumes/`.
- Check `php.ini` `upload_max_filesize` and `post_max_size` — increase them if large files fail.

### Subcategory dropdown not loading when selecting a job category
- Open the browser developer console (F12) and check for a failed XHR request to `get_job_subcategories.php`.
- Confirm the file exists and `database.php` is reachable from it.

### Application approve/reject button not responding
- Check the console for the JSON response from `approve_application.php`.
- Confirm both `accepted_application` and `accepted_student` tables exist in the database.
- Confirm the logged-in teacher owns the job post (the handler validates ownership).

### Notifications not appearing
- Confirm the `notification` table exists with columns: `notification_id`, `user_id`, `role_id`, `event_type`, `reference_table`, `reference_id`, `message`, `status`, `created_at`.
- Check `jobapply.php` for the INSERT query and verify column names match your schema.
