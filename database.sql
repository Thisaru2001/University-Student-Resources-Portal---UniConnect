CREATE DATABASE IF NOT EXISTS uniconnect;
USE uniconnect;

-- create department table.
CREATE TABLE IF NOT EXISTS departments (
    department_id   INT             AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(150)    NOT NULL
);

-- created academic_years table.
CREATE TABLE IF NOT EXISTS academic_years (
    year_id INT             AUTO_INCREMENT PRIMARY KEY,
    year    VARCHAR(20)     NOT NULL
);

-- created students table.
CREATE TABLE IF NOT EXISTS students (
    student_id      VARCHAR(20)     PRIMARY KEY,
    fname           VARCHAR(150)    NOT NULL,
    lname           VARCHAR(150)    NOT NULL,
    password        VARCHAR(255)    NOT NULL,
    profile_image   VARCHAR(255),
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- created admins table.
CREATE TABLE IF NOT EXISTS admins (
    admin_id    INT             AUTO_INCREMENT PRIMARY KEY,
    student_id  VARCHAR(50)     NOT NULL,
    fname       VARCHAR(100)    NOT NULL,
    lname       VARCHAR(100)    NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- created type table.
CREATE TABLE IF NOT EXISTS type (
    id      INT             AUTO_INCREMENT PRIMARY KEY,
    type    VARCHAR(150)    NOT NULL
);

-- created courses table.
CREATE TABLE IF NOT EXISTS courses (
    course_id       INT             AUTO_INCREMENT PRIMARY KEY,
    course_code     VARCHAR(20)     NOT NULL,
    course_name     VARCHAR(150)    NOT NULL,
    department_id   INT             NOT NULL,
    year_id         INT             NOT NULL,
    semester        TINYINT         NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE,
    FOREIGN KEY (year_id)       REFERENCES academic_years(year_id)    ON DELETE CASCADE
);

-- created resources table.
CREATE TABLE IF NOT EXISTS resources (
    resource_id         INT             AUTO_INCREMENT PRIMARY KEY,
    file_name           VARCHAR(255)    NOT NULL,
    description         TEXT,
    file_path           VARCHAR(255)    NOT NULL,
    type_id             INT,
    course_id           INT,
    anonymous_upload    TINYINT(1)      DEFAULT 0,
    uploaded_at         TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    student_id          VARCHAR(20),
    type                INT,
    FOREIGN KEY (type_id)    REFERENCES type(id)               ON DELETE SET NULL,
    FOREIGN KEY (course_id)  REFERENCES courses(course_id)     ON DELETE SET NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id)   ON DELETE SET NULL
);
