CREATE DATABASE user_management;

USE user_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create an admin account with bcrypt password. 
-- default password for this below query is 'abc123456'
INSERT INTO users (username, email, password, role)
VALUES ('admin3', 'admin3@example.com', '$2a$12$dwmiBkApw5QpoPW37ZI3uOA86qyJopjCdAZs5jExY0pYND9nmIND2', 'admin');


CREATE TABLE recommendation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recommendation_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    relationship VARCHAR(255) NOT NULL,
    comments TEXT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES recommendation_requests(id) ON DELETE CASCADE
);