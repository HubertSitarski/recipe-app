-- Grant all privileges to the app user from any host
CREATE USER IF NOT EXISTS 'app'@'%' IDENTIFIED BY 'asdf123';
GRANT ALL PRIVILEGES ON app_test.* TO 'app'@'%';
FLUSH PRIVILEGES; 