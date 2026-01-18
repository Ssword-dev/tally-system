--                  schema.sql            
-- This file is responsible for initializing
-- the database, this is what gets executed when
-- running `php init` command, which sets up
-- the database schema for the application.
-- note that the command may also setup other things
-- such as more features, or seeding data (data presets)

-- === Database ===
DROP DATABASE IF EXISTS `student_database_app_db`;

-- the reason why these are set is
-- to ensure proper UTF-8 support including emojis
-- and to ensure proper sorting and comparison of strings
-- innodb is used instead of the default engine 'myisam'
-- to ensure foreign key (which i use for relating records together)
-- works as expected.
-- without innodb, foreign key constraints are ignored.

-- here is what will happen if no innodb:
-- - deleting a teacher will NOT delete their classes
-- - ownership model will not work, records will have broken references
-- - etc.

CREATE DATABASE `student_database_app_db` 
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `student_database_app_db`;

-- teachers (the actual users of this app)
CREATE TABLE `teachers_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- basic metadata
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,

    `email` VARCHAR(100) NOT NULL,
    `contact_number` VARCHAR(20),
    `address` VARCHAR(150) NULL,

    -- authentication
    `password_hash` VARCHAR(60) NOT NULL,

    -- timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `unique_teacher_email`
        UNIQUE (`email`)
) ENGINE = InnoDB;

-- classes (teacher-owned)
CREATE TABLE `classes_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- ownership
    `teacher_id` INT NOT NULL,
    `created_by` INT NOT NULL,

    -- descriptive fields
    `name` VARCHAR(50) NOT NULL,        -- e.g. "grade 8 - section a"
    `course_name` VARCHAR(50) NOT NULL, -- e.g. "mathematics"
    `school_year` VARCHAR(20) NOT NULL,  -- e.g. "2024-2025"

    -- timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `unique_teacher_class`
        UNIQUE (`teacher_id`, `name`, `course_name`, `school_year`)
) ENGINE = InnoDB;

-- students (owned by class, created by teacher)
-- =========================================================
CREATE TABLE `students_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- ownership
    `class_id` INT NOT NULL,
    `created_by` INT NOT NULL,

    -- student identity
    `lrn` VARCHAR(20) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(55) NOT NULL,

    `email` VARCHAR(100) NULL,
    `contact_number` VARCHAR(30) NULL,

    `guardian` VARCHAR(100) NULL,
    `guardian_contact_number` VARCHAR(30) NULL,

    -- timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `unique_student_per_class`
        UNIQUE (`class_id`, `lrn`)
) ENGINE = InnoDB;

-- activity types (class-scoped grading structure)
CREATE TABLE `activity_types_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- ownership
    `class_id` INT NOT NULL,
    `created_by` INT NOT NULL,

    `name` VARCHAR(100) NOT NULL, -- quiz, exam, project
    `weight` INT NOT NULL,        -- percentage weight

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `unique_activity_type_per_class`
        UNIQUE (`class_id`, `name`)
) ENGINE = InnoDB;

-- activities (owned by a class)
CREATE TABLE `activities_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- ownership
    `class_id` INT NOT NULL,
    `type_id` INT NOT NULL,
    `created_by` INT NOT NULL,

    `name` VARCHAR(100) NOT NULL,
    `maximum_score` INT NOT NULL,

    -- timestamps
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT `unique_activity_per_class`
        UNIQUE (`class_id`, `name`)
) ENGINE = InnoDB;

-- scores (pure score records, created by a teacher)
CREATE TABLE `scores_tbl` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,

    -- ownership
    `activity_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `created_by` INT NOT NULL,

    `score` INT NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `unique_score_per_activity_student`
        UNIQUE (`activity_id`, `student_id`)
) ENGINE = InnoDB;

-- indexes (ownership + performance)
CREATE INDEX `idx_classes_teacher` ON `classes_tbl` (`teacher_id`);
CREATE INDEX `idx_students_class` ON `students_tbl` (`class_id`);
CREATE INDEX `idx_activities_class` ON `activities_tbl` (`class_id`);
CREATE INDEX `idx_scores_student` ON `scores_tbl` (`student_id`);
CREATE INDEX `idx_scores_activity` ON `scores_tbl` (`activity_id`);

-- =========================================================
-- Foreign Key Constraints (added late to avoid dependency graph issues)
-- =========================================================
ALTER TABLE `classes_tbl`
    ADD CONSTRAINT `fk_classes_teacher`
        FOREIGN KEY (`teacher_id`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_classes_created_by`
        FOREIGN KEY (`created_by`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE RESTRICT;

ALTER TABLE `students_tbl`
    ADD CONSTRAINT `fk_students_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `classes_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_students_created_by`
        FOREIGN KEY (`created_by`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE RESTRICT;

ALTER TABLE `activity_types_tbl`
    ADD CONSTRAINT `fk_activity_types_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `classes_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_activity_types_created_by`
        FOREIGN KEY (`created_by`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE RESTRICT;

ALTER TABLE `activities_tbl`
    ADD CONSTRAINT `fk_activities_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `classes_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_activities_type`
        FOREIGN KEY (`type_id`)
        REFERENCES `activity_types_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_activities_created_by`
        FOREIGN KEY (`created_by`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE RESTRICT;

ALTER TABLE `scores_tbl`
    ADD CONSTRAINT `fk_scores_activity`
        FOREIGN KEY (`activity_id`)
        REFERENCES `activities_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_scores_student`
        FOREIGN KEY (`student_id`)
        REFERENCES `students_tbl` (`id`)
        ON DELETE CASCADE,
    ADD CONSTRAINT `fk_scores_created_by`
        FOREIGN KEY (`created_by`)
        REFERENCES `teachers_tbl` (`id`)
        ON DELETE RESTRICT;