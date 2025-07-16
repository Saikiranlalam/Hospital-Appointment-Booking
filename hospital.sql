CREATE DATABASE IF NOT EXISTS hospital;
USE hospital;
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  type VARCHAR(50),
  date DATE,
  doctor VARCHAR(100),
  status VARCHAR(20) DEFAULT 'Pending'
);
