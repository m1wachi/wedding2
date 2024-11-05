CREATE TABLE guest (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    rsvp_status ENUM('Yes', 'No', 'Maybe'),
    no_of_guest INT DEFAULT 0
);
