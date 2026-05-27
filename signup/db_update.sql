CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(20) UNIQUE,
  fname VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(255)
);