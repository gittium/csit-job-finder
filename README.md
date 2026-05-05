# CSIT Job Finder — Admin Panel Documentation

A comprehensive administration dashboard for managing users, roles, permissions, and reports on the **CSIT Job Finder & Skill Development Platform** at Naresuan University's Computer Science and Information Technology department.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [Directory Structure](#3-directory-structure)
4. [Database Schema Overview](#4-database-schema-overview)
5. [Installation & Setup](#5-installation--setup)
6. [Accessing the Admin Panel](#6-accessing-the-admin-panel)
7. [Feature Guide](#7-feature-guide)
   - [Dashboard](#71-dashboard--home_pagephp)
   - [User Management](#72-user-management--manage_usersphp)
   - [Role Management](#73-role-management--role_pagephp)
   - [Permission Management](#74-permission-management--permission_pagephp)
   - [Reports Management](#75-reports-management--reportsphp)
   - [Student Profile View](#76-student-profile-view--admin_student_profilephp)
   - [Teacher Profile View](#77-teacher-profile-view--admin_teacher_profilephp)
8. [AJAX API Reference](#8-ajax-api-reference)
9. [Common Issues & Troubleshooting](#9-common-issues--troubleshooting)

---

## 1. Project Overview

The full platform consists of two applications:

| Directory | Purpose |
|---|---|
| `csit-job-finder-main` | Main app for students and professors — job posts, applications, reviews |
| `csit-job-finder-admin` | This admin panel — user, role, permission, and report management |

The admin panel is a **standalone PHP application** that connects to the same `ip` MySQL database as the main app. It is session-protected — every page redirects unauthenticated visitors to `../login.php`.

**User roles on the platform:**
- **Student (role_id 4)** — browse jobs, apply, manage profile & skills
- **Teacher/Professor (role_id 3)** — post jobs, manage applications, review students
- **Admin** — this panel
- **Executive** — analytics dashboard (separate interface)

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (procedural + MySQLi) |
| Database | MySQL (database name: `ip`) |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Web Server | Apache (via XAMPP / WAMP / MAMP) |
| Icons | Bootstrap Icons (CDN) |
| Fonts | Google Fonts — Roboto, Montserrat |

---

## 3. Directory Structure

```
csit-job-finder-admin/
│
├── Home_page.php              # Dashboard home — metrics overview
├── manage_users.php           # Activate / disable student & teacher accounts
├── Role_page.php              # Create & edit system roles (bilingual EN/TH)
├── Permission_page.php        # Manage skills, hobbies, and job categories
├── Permission_action.php      # AJAX backend handler for permission CRUD
├── reports.php                # Review and resolve flagged job posts
├── admin_student_profile.php  # Detailed view of a student account
├── admin_teacher_profile.php  # Detailed view of a teacher account
├── joinustest.php             # View a reported job post detail
├── review.php                 # Review records
├── calculate_review.php       # Review score calculation helper
│
├── db_connect.php             # Database connection (used by Permission_page)
├── database.php               # Database connection (used by manage_users, reports)
│
├── siderbar.php               # Sidebar navigation HTML component
├── script_sidebar.js          # Sidebar toggle behaviour
├── script_home.js             # Dashboard JS
├── script_permission.js       # All permission CRUD modals and search
├── script_role.js             # Role page helpers
├── script_logout.js           # Logout handler
│
├── style_home.css             # Dashboard styles
├── style_sidebar.css          # Sidebar styles
├── style_role.css             # Role page styles
├── style_permission.css       # Permission page styles
├── style_logout.css           # Logout button styles
│
├── css/                       # Additional per-page stylesheets
│   ├── style_managruser.css
│   └── style_reports.css
│
└── js/                        # Additional per-page scripts
    ├── script_manage_users.js
    └── script_reports.js
```

---

## 4. Database Schema Overview

Tables directly managed by the admin panel:

| Table | Description |
|---|---|
| `user` | All accounts — stores `role_id` and `role_status_id` |
| `student` | Student profile data (name, email, etc.) |
| `teacher` | Teacher profile data (name, email, etc.) |
| `role` | System roles with English and Thai names |
| `skill` | Top-level technical skill categories |
| `subskill` | Sub-skills linked to a parent `skill` |
| `hobby` | Top-level hobby / interest categories |
| `subhobby` | Sub-hobbies linked to a parent `hobby` |
| `job_category` | Top-level job classification categories |
| `job_subcategory` | Job sub-types linked to a parent `job_category` |
| `report` | Student-submitted reports on job posts |
| `post_job` | Job posts created by teachers |

Key status values:

| Field | Value | Meaning |
|---|---|---|
| `role_status_id` | `1` | Account is active |
| `role_status_id` | `2` | Account is disabled |
| `report_status_id` | `1` | Report is pending review |
| `report_status_id` | `2` | Report is closed |
| `job_status_id` | `3` | Job post has been deleted by admin |

---

## 5. Installation & Setup

### Prerequisites

- **XAMPP** (recommended) or any LAMP/WAMP/MAMP stack
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache

### Step 1 — Install XAMPP

Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org). Start both the **Apache** and **MySQL** modules from the XAMPP Control Panel.

### Step 2 — Place the project files

Copy both project directories into your Apache web root:

```
C:\xampp\htdocs\
├── csit-job-finder-main\
└── csit-job-finder-admin\
```

> On macOS with XAMPP the web root is `/Applications/XAMPP/htdocs/`.

### Step 3 — Create the database

1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Click **New** in the left sidebar.
3. Enter the database name `ip` and click **Create**.
4. Select the `ip` database, click the **Import** tab.
5. Click **Choose File**, select the provided `.sql` dump file, and click **Go**.

### Step 4 — Configure the database connection

Open `csit-job-finder-admin/db_connect.php` (and `database.php` if it differs) and verify the credentials match your MySQL setup:

```php
$servername = "localhost";
$username   = "root";   // default XAMPP username
$password   = "";       // default XAMPP has no password
$dbname     = "ip";
```

If you have set a MySQL root password, update `$password` accordingly.

### Step 5 — Verify the main app login path

`Home_page.php`, `manage_users.php`, and `reports.php` redirect unauthenticated users to `../login.php`. Ensure the main app (`csit-job-finder-main`) is placed one level up from the admin panel in your web root, or update the redirect path in those files to match your folder structure.

### Step 6 — Open in browser

Navigate to:

```
http://localhost/csit-job-finder-admin/Home_page.php
```

You will be redirected to the login page if no session is active.

---

## 6. Accessing the Admin Panel

Log in through the main application's login page using admin credentials:

| Field | Value |
|---|---|
| User ID | `admin0141` |
| Password | `admin1234` |

After login, a PHP session is created with `$_SESSION['user_id']`. All admin pages check for this session key and redirect to `../login.php` if it is missing.

> **Security note:** The default credentials above are for development. Change them before deploying to any shared or public environment.

---

## 7. Feature Guide

### 7.1 Dashboard — `Home_page.php`

The landing page after login. Displays three summary cards:

| Card | What it shows |
|---|---|
| Users | Total number of accounts in the `user` table |
| Roles | All system roles listed as clickable tags (links to Role Management) |
| Reports | Total number of entries in the `report` table |

**Navigation sidebar** (loaded via `siderbar.php` + `script_sidebar.js`):

| Menu item | Destination |
|---|---|
| Home | `Home_page.php` |
| Manage User | `manage_users.php` |
| Reports | `reports.php` |
| General > Role | `Role_page.php` |
| General > Permission | `Permission_page.php` |
| Logout | Ends session and redirects to login |

On mobile (viewport ≤ 768 px) the sidebar collapses. Tap the **☰ Menu** button to toggle it open.

---

### 7.2 User Management — `manage_users.php`

Manage student and teacher accounts in a single paginated table.

#### Viewing users

- Displays **5 users per page** by default.
- Shows columns: Name, Email, Status (Active / Disabled), Actions.
- Teachers (role_id 3) and students (role_id 4) are fetched using a `UNION` query across the `user`, `student`, and `teacher` tables.

#### Filtering by role

Use the **Role** dropdown at the top:

| Option | Shows |
|---|---|
| ทั้งหมด (All) | Both students and teachers |
| อาจารย์ (Teacher) | Teachers only |
| นิสิต (Student) | Students only |

Selecting an option reloads the page with `?role=teacher` or `?role=student` appended to the URL.

#### Searching

Type a name or email in the **Search** field and click **Search**. The query filters on `stu_name`, `stu_email`, `teach_name`, or `teach_email` using a `LIKE` match.

#### Activating / Disabling an account

Each row has an action button:

- If the account is **Active** (`role_status_id = 1`), the button shows **Disable**.
- If the account is **Disabled** (`role_status_id = 2`), the button shows **Activate**.

Clicking either button sends an **AJAX POST** request to the same `manage_users.php` file with `user_id` and `status`. The page updates the button and status text **without a full reload**.

| POST field | Value |
|---|---|
| `user_id` | The user's ID |
| `status` | `1` (activate) or `2` (disable) |

The server responds with JSON:
```json
{ "success": true, "new_status": 1 }
```

#### Viewing a profile

Click **View** to navigate to the user's detailed profile page:
- Students → `admin_student_profile.php?student_id={id}`
- Teachers → `admin_teacher_profile.php?teacher_id={id}`

#### Pagination

Navigation links appear below the table: **Previous**, numbered pages, **Next**. The current page and role filter are preserved in all pagination links.

---

### 7.3 Role Management — `Role_page.php`

Create and edit system roles that appear throughout the platform.

#### Viewing roles

Roles are displayed in a table with two columns:

| Column | Description |
|---|---|
| Role (EN) | English role name |
| Role (TH) | Thai role name |

Use the **Show** dropdown to change rows per page (options: 2, 5, 10). The page reloads with `?entries=N` in the URL.

#### Searching

Type in the **Search** field. This filters rows **client-side in real time** — no page reload. Both English and Thai columns are searched.

#### Creating a role

1. Click the **+ Create Role** button (top right).
2. A modal dialog appears with two fields:
   - **Enter role name (EN)** — English name (required)
   - **Enter role name (TH)** — Thai name (required; defaults to `ไม่ระบุ` if left blank)
3. Click **Confirm** to submit the form via POST.

The new role is inserted into the `role` table and the page reloads.

#### Editing a role

1. Click the **Edit** button on any row.
2. A modal pre-filled with the current English and Thai names appears.
3. Update either field and click **Save**.

The `role` table is updated via a prepared statement (`UPDATE role SET role_name = ?, role_name_th = ? WHERE role_id = ?`).

---

### 7.4 Permission Management — `Permission_page.php`

Manages all hierarchical taxonomies used across the platform. The page is divided into three sections.

> All create and edit operations are sent as AJAX requests to `Permission_action.php`, which returns a JSON response. The page does **not** reload on save.

---

#### Section 1 — Skill & Subskill

**Skill** is a top-level technical category (e.g., "Programming", "Design").  
**Subskill** is a specific skill that belongs to a parent Skill (e.g., "Python" under "Programming").

| Action | How to do it |
|---|---|
| Search skills | Type in the **Search** field above the skill list — filters client-side in real time |
| Add a skill | Click **+ เพิ่มทักษะ** → enter the skill name in the modal → click Save |
| Edit a skill | Click **แก้ไข** next to the skill → update the name in the modal → click Save |
| Add a subskill | Click **+ เพิ่มทักษะย่อย** → enter the subskill name → click Save |
| Edit a subskill | Click **แก้ไข** next to the subskill → update the name → click Save |

Checking a skill's checkbox reveals its associated subskills (controlled via `script_permission.js`).

---

#### Section 2 — Hobby & Subhobby

**Hobby** is a top-level interest area (e.g., "Sports").  
**Subhobby** is a specific interest linked to a parent Hobby (e.g., "Football" under "Sports").

The interface and actions are identical to Skill & Subskill:

| Button | Action |
|---|---|
| **+ เพิ่มงานอดิเรก** | Add a new hobby |
| **+ เพิ่มงานอดิเรกย่อย** | Add a new subhobby |
| **แก้ไข** | Edit the selected item |

---

#### Section 3 — Job Category & Job Subcategory

**Job Category** is a top-level job classification (e.g., "Engineering").  
**Job Subcategory** is a specific job type under a category (e.g., "Backend Developer" under "Engineering").

| Button | Action |
|---|---|
| **+ เพิ่มประเภทงาน** | Add a new job category |
| **+ เพิ่มงานย่อย** | Add a new job subcategory |
| **แก้ไข** | Edit the selected item |

---

### 7.5 Reports Management — `reports.php`

View and resolve job posts that have been flagged by students.

#### Viewing reports

The table shows all **pending** reports (`report_status_id = 1`):

| Column | Description |
|---|---|
| Reporter | Name of the student who submitted the report |
| Post | Title of the reported job post |
| Actions | View / Delete / Cancel buttons |

5 reports are shown per page with standard pagination controls.

#### Searching

Type a reporter name or job post title in the **Search** field and click **Search**. The URL updates with `?search=...`.

#### Taking action on a report

Each report row has three action buttons:

| Button | Effect |
|---|---|
| **View** | Opens `joinustest.php?id={post_job_id}` to show the full job post detail |
| **Delete** | Sets the job post's `job_status_id = 3` (deleted) and closes the report (`report_status_id = 2`). A confirmation dialog appears before proceeding. |
| **Cancel** | Closes the report without removing the job post (`report_status_id = 2`). Indicates the post does not violate the rules. |

Both Delete and Cancel are submitted as HTML form POST requests and redirect back to the same page on success.

---

### 7.6 Student Profile View — `admin_student_profile.php`

Accessed from User Management by clicking **View** on a student row.

**URL format:** `admin_student_profile.php?student_id={id}`

Displays:
- Student name, email, and profile image
- Assigned skills and subskills
- Assigned hobbies and subhobbies

Admins can update a student's skill and hobby assignments directly from this page.

---

### 7.7 Teacher Profile View — `admin_teacher_profile.php`

Accessed from User Management by clicking **View** on a teacher row.

**URL format:** `admin_teacher_profile.php?teacher_id={id}`

Displays:
- Teacher name, email, and profile information

---

## 8. AJAX API Reference

### User status update — `manage_users.php`

**Method:** POST  
**Trigger:** Activate / Disable button click

| Parameter | Type | Description |
|---|---|---|
| `user_id` | string | ID of the user to update |
| `status` | int | `1` = activate, `2` = disable |

**Success response:**
```json
{ "success": true, "new_status": 1 }
```

**Error response:**
```json
{ "success": false, "error": "error message here" }
```

---

### Permission CRUD — `Permission_action.php`

**Method:** POST  
**Content-Type:** `application/x-www-form-urlencoded`

Supported `action` values:

| action | Required fields | Description |
|---|---|---|
| `add_skill` | `name` | Add a new skill |
| `edit_skill` | `id`, `name` | Edit an existing skill |
| `add_subskill` | `name`, `skill_id` | Add a new subskill |
| `edit_subskill` | `id`, `name` | Edit an existing subskill |
| `add_hobby` | `name` | Add a new hobby |
| `edit_hobby` | `id`, `name` | Edit an existing hobby |
| `add_subhobby` | `name`, `hobby_id` | Add a new subhobby |
| `edit_subhobby` | `id`, `name` | Edit an existing subhobby |
| `add_job_category` | `name` | Add a new job category |
| `edit_job_category` | `id`, `name` | Edit an existing job category |
| `add_job_subcategory` | `name`, `job_category_id` | Add a new job subcategory |
| `edit_job_subcategory` | `id`, `name` | Edit an existing job subcategory |

**Success response:**
```json
{ "success": true }
```

**Error response:**
```json
{ "success": false, "error": "error message here" }
```

---

## 9. Common Issues & Troubleshooting

### Page shows blank or "Connection failed"

- Confirm that Apache and MySQL are running in the XAMPP Control Panel.
- Check that `db_connect.php` and `database.php` have the correct `$username` and `$password` values.
- Ensure the database name is `ip` and the schema has been imported.

### Redirected to login on every page load

- The admin panel stores authentication state in a PHP session. Make sure `session.save_path` is writable on your server.
- In XAMPP on Windows, sessions are stored in `C:\xampp\tmp`. Verify this folder exists and has write permissions.

### Images not loading in report detail view (`joinustest.php`)

- The file contains a hardcoded base URL: `$base_url = "http://localhost/P6/admin/"`. Update this to match your actual folder path, e.g. `"http://localhost/csit-job-finder-admin/"`.

### Activate / Disable button not responding

- Open the browser developer console (F12). An AJAX error will appear if `manage_users.php` returned an unexpected response.
- Confirm that the `user` table has a `role_status_id` column.

### Permission changes not saving

- Check the browser console for the JSON response from `Permission_action.php`.
- Confirm that `Permission_action.php` is in the same directory as `Permission_page.php`.
- Ensure the database user has `INSERT` and `UPDATE` privileges on the `ip` database.
