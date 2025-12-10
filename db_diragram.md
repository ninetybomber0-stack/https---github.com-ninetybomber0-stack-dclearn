# Database Diagram

This document outlines the current database schema for the application.

## Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    tb_member {
        int id PK
        varchar std_id
        varchar name
        varchar sname
        varchar tel
        varchar email
        varchar pass
        varchar id_line
        varchar type
        timestamp time_reg
    }

    tb_content {
        int id PK
        varchar name
        varchar lesson_id_text UK "e.g. w1-vid"
        varchar title
        int lesson_order
        varchar video_path_720p
        varchar video_path_480p
        varchar poster_path
        varchar slide_url
        varchar quiz_url
        int score
        datetime created_at
    }

    tb_test {
        int id PK
        int lesson_id FK
        varchar lesson_id_text
        text question
        varchar choice_a
        varchar choice_b
        varchar choice_c
        varchar choice_d
        enum correct "A, B, C, D"
        int timeshow "Seconds to show question"
        datetime updated_at
    }

    tb_test_log {
        bigint id PK
        int quiz_id FK
        varchar lesson
        enum answer "A, B, C, D"
        enum correct "A, B, C, D"
        tinyint is_correct
        int user_id FK
        datetime created_at
    }

    tb_transcripts {
        int id PK
        varchar lesson_id_text
        int cue_time
        text text
    }

    tb_scores {
        int id PK
        int member_id
        int lesson_id
        int score
        int total
        datetime created_at
    }

    user_lesson_status {
        int id PK
        varchar user_id
        int lesson_id
        tinyint pre_test_completed
        tinyint post_test_completed
        timestamp created_at
        timestamp updated_at
    }

    %% Relationships
    tb_content ||--o{ tb_test : "has questions"
    tb_test ||--o{ tb_test_log : "has logs"
    tb_member ||--o{ tb_test_log : "logs answers"
    tb_content ||--o{ tb_scores : "has scores"
    tb_content ||--o{ user_lesson_status : "tracks status"
    
    %% Note: Some relationships are implied by column names but not enforced by foreign keys in all tables.
```

## Table Descriptions

### 1. `tb_member`
Stores user/student information.
- **Primary Key**: `id`
- **Key Columns**: `std_id` (Student ID), `email`, `pass` (Password).

### 2. `tb_content`
Stores metadata for lessons/videos.
- **Primary Key**: `id`
- **Unique Key**: `lesson_id_text` (String identifier for the lesson, e.g., 'w1-vid').
- **Columns**: Stores paths to video files (720p, 480p), poster images, and external URLs for slides/quizzes.

### 3. `tb_test`
Stores the questions associated with each lesson.
- **Primary Key**: `id`
- **Foreign Key**: `lesson_id` (Links to `tb_content.id`).
- **Columns**: Contains the question text, 4 choices (A-D), the correct answer, and `timeshow` (when to show the question in the video).

### 4. `tb_test_log`
Logs every answer attempt by a user for a specific question.
- **Primary Key**: `id`
- **Foreign Keys**: 
    - `quiz_id` (Links to `tb_test.id`)
    - `user_id` (Links to `tb_member.id`)
- **Columns**: Records the user's answer, whether it was correct, and the timestamp.

### 5. `tb_transcripts`
Stores subtitles or transcripts for the video lessons.
- **Primary Key**: `id`
- **Columns**: `lesson_id_text` (links to lesson), `cue_time` (timestamp in seconds), and the transcript `text`.

### 6. `tb_scores`
Stores the aggregate score for a student on a specific lesson.
- **Primary Key**: `id`
- **Columns**: `member_id` (links to `tb_member.id`), `lesson_id`, `score`, and `total` possible score.

### 7. `user_lesson_status`
Tracks whether a user has completed the pre-test and post-test for a lesson.
- **Primary Key**: `id`
- **Columns**: `user_id`, `lesson_id`, and boolean flags for completion status.
