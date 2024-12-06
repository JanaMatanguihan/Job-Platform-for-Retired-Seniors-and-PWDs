CREATE SCHEMA Gabay;

USE Gabay;

select * from user_auth;

CREATE TABLE user_auth (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    pass VARCHAR(255) NOT NULL
);

CREATE TABLE user_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name varchar(255) not null,
    dob DATE,
    gender VARCHAR(20) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user_auth(user_id) ON DELETE CASCADE
);

CREATE TABLE contact_info (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    phone_num VARCHAR(30) UNIQUE NOT NULL,
    address VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user_auth(user_id) ON DELETE CASCADE
);

CREATE TABLE application_form (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file BLOB,
    FOREIGN KEY (user_id) REFERENCES user_auth(user_id) ON DELETE CASCADE
);