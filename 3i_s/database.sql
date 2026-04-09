

CREATE DATABASE IF NOT EXISTS fepc_medical_db;
USE fepc_medical_db;


CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO admins (username, password) VALUES 
('admin', '$2y$12$VUW3jt8NfcvRZb/SzxsOC.iZYuWmy7gUsy9PN0xItFOlDV7FYcAtS');


CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    unique_code VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    age INT NOT NULL,
    academic_year VARCHAR(20),
    grade_level VARCHAR(20) NOT NULL,
    strand VARCHAR(20) NOT NULL,
    guardian_name VARCHAR(100),
    address TEXT,
    emergency_contact VARCHAR(50),
    relationship VARCHAR(50),
    added_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS medical_patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(30) NOT NULL UNIQUE,
    student_id INT NOT NULL,
    conditions TEXT NOT NULL,
    severity ENUM('Standard', 'Critical') DEFAULT 'Standard',
    notes TEXT,
    registered_date DATETIME,
    registered_by VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS medical_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id VARCHAR(30) NOT NULL UNIQUE,
    patient_id INT NOT NULL,
    visit_date DATE NOT NULL,
    time_in TIME NOT NULL,
    time_out TIME,
    duration INT,
    officer_id INT,
    notes TEXT,
    treatment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES medical_patients(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS officers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    officer_id VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    license_number VARCHAR(50),
    contact VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS officer_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id VARCHAR(30) NOT NULL UNIQUE,
    officer_id INT NOT NULL,
    log_date DATE NOT NULL,
    time_in TIME NOT NULL,
    time_out TIME,
    duration INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (officer_id) REFERENCES officers(id) ON DELETE CASCADE
);

