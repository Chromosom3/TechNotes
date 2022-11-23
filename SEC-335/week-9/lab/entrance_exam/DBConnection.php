<?php
if(!is_dir(__DIR__.'./db'))
    mkdir(__DIR__.'./db');
if(!defined('db_file')) define('db_file',__DIR__.'./db/exam_db.db');
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        $this->open(db_file);
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        $this->exec("CREATE TABLE IF NOT EXISTS `admin_list` (
            `admin_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL Default 1,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        //User Comment
        // Type = [ 1 = Administrator, 2 = Cashier]
        // Status = [ 1 = Active, 2 = Inactive]

        $this->exec("CREATE TABLE IF NOT EXISTS `settings_list` (
            `meta_field` TEXT NOT NULL,
            `meta_value` TEXT NOT NULL
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `department_list` (
            `department_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `course_list` (
            `course_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `department_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`department_id`) REFERENCES `department_list`(`department_id`) ON DELETE CASCADE
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `exam_set_list` (
            `exam_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `is_for_all_course` INTEGER NOT NULL DEFAULT 1,
            `course_ids` TEXT NULL,
            `duration` REAL NOT NULL DEFAULT 0,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP
        ) ");

        $this->exec("CREATE TABLE IF NOT EXISTS `question_category_list` (
            `qcategory_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `exam_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `order` INTEGER NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`exam_id`) REFERENCES `exam_set_list`(`exam_id`) ON DELETE CASCADE
        ) ");
        
        $this->exec("CREATE TABLE IF NOT EXISTS `question_list` (
            `question_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `qcategory_id` INTEGER NOT NULL,
            `question` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`qcategory_id`) REFERENCES `question_category_list`(`qcategory_id`) ON DELETE CASCADE
        ) ");

        $this->exec("CREATE TABLE IF NOT EXISTS `option_list` (
            `option_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `question_id` INTEGER NOT NULL,
            `option` TEXT NOT NULL,
            `is_right` INTEGER NOT NULL DEFAULT 0,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`question_id`) REFERENCES `question_list`(`question_id`) ON DELETE CASCADE
        ) ");

        $this->exec("CREATE TABLE IF NOT EXISTS `enrollee_list` (
            `enrollee_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `reference_code` TEXT NOT NULL,
            `firstname` TEXT NOT NULL,
            `middlename` TEXT NULL,
            `lastname` TEXT NOT NULL,
            `course_id` INTEGER NOT NULL,
            `email` TEXT NOT NULL,
            `gender` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `address` TEXT NOT NULL,
            `status` TEXT NOT NULL DEFAULT 1,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`course_id`) REFERENCES `course_list`(`course_id`) ON DELETE CASCADE
        ) ");
         $this->exec("CREATE TABLE IF NOT EXISTS `answered_list` (
            `answered_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `enrollee_id` INTEGER NOT NULL,
            `exam_id` INTEGER NOT NULL,
            `score` INTEGER NOT NULL DEFAULT 0,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`enrollee_id`) REFERENCES `enrollee_list`(`enrollee_id`) ON DELETE CASCADE,
            FOREIGN KEY (`exam_id`) REFERENCES `exam_set_list`(`exam_id`) ON DELETE CASCADE
        ) ");
        $this->exec("CREATE TABLE IF NOT EXISTS `answer_items` (
            `answered_id` INTEGER NOT NULL,
            `question_id` INTEGER NOT NULL,
            `option_id` INTEGER NOT NULL,
            `is_right` INTEGER NOT NULL DEFAULT 0,
            `date_created` TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP,
            FOREIGN KEY (`answered_id`) REFERENCES `answered_list`(`answered_id`) ON DELETE CASCADE,
            FOREIGN KEY (`question_id`) REFERENCES `question_list`(`question_id`) ON DELETE CASCADE,
            FOREIGN KEY (`option_id`) REFERENCES `option_list`(`option_id`) ON DELETE CASCADE
        ) ");


        // payroll_list Notes
        // payroll_type = [ 1= Monthly, 2,= Semi-Monthly]


        // $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_prod AFTER UPDATE on `vacancy_list`
        // BEGIN
        //     UPDATE `vacancy_list` SET date_updated = CURRENT_TIMESTAMP where vacancy_id = vacancy_id;
        // END
        // ");
        $this->exec("INSERT or IGNORE INTO `admin_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");

    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();