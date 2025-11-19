# Project: DC-Learn - E-Learning Platform

## Overview

DC-Learn is a web-based e-learning platform designed for students to access educational content. It provides video lessons, announcements, quizzes, and tests. The application is built as a monolithic PHP application with a simple routing mechanism based on URL parameters.

## Technology Stack

*   **Backend**: PHP
*   **Database**: MySQL / MariaDB
*   **Web Server**: Apache (as part of the AppServ WAMP stack)
*   **Frontend**:
    *   HTML5
    *   Bootstrap 5 for styling and components
    *   Vanilla JavaScript for client-side interactivity

## Project Structure

The project is located in the `c:\AppServ\www` directory, which serves as the web root.

```
c:\AppServ\www\
├── config/              # Main configuration files (DB connection, session)
├── dclearn/             # Core e-learning application module
│   ├── config/          # Module-specific configuration (contains a conflicting DB config)
│   ├── index.php        # Main entry point/router for the e-learning module
│   ├── module/          # Contains application logic like session handling
│   └── page/            # UI pages/views for the e-learning module
├── img/                 # Image assets
├── page/                # Contains various PHP pages for different functionalities
├── videos/              # Video assets
├── kunuengc_dcl.sql     # Database dump with schema and some data
├── User.php             # Page for editing user information
└── ...
```

### Key Components

*   **`dclearn/index.php`**: The main entry point for the e-learning application. It handles session management, user authentication, and routing to different pages based on the `?page=` query parameter.
*   **`config/connect.php`**: The primary database connection script.
*   **`kunuengc_dcl.sql`**: Contains the database schema. Key tables include `tb_member` (users), `tb_content` (lessons), `tb_notice` (announcements), and tables for tests/scores.
*   **`page/home.php`**: The student dashboard, showing announcements and available lessons.
*   **`page/content.php`**: The video lesson player interface.

## Identified Issues

This analysis has identified several critical issues:

1.  **Conflicting Database Configurations**: There are two database configuration files (`config/connect.php` and `dclearn/config/connect.php`) with different passwords. This will cause connection failures.
2.  **Database Schema Mismatch**: The `User.php` page tries to update an `email` column in the `tb_member` table, but this column does not exist in the schema.
3.  **SQL Injection Vulnerability**: The code contains SQL queries with directly embedded variables (e.g., in `dclearn/index.php`), posing a serious security risk. Prepared statements should be used instead.
4.  **Hardcoded Content**: Lesson data (video sources, transcripts) is hardcoded in JavaScript in `page/content.php` instead of being loaded dynamically from the database.
