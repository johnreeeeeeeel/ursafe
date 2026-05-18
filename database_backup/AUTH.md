# AUTH



##### GET ADMIN BY EMAIL (URSAFE\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAdminByEmail(IN p\_email VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       NULL AS lastname,

&#x20;       NULL AS firstname,

&#x20;       NULL AS middlename,

&#x20;       NULL AS sex,

&#x20;       NULL AS dob,

&#x20;       NULL AS institute,

&#x20;       NULL AS program,

&#x20;       username,

&#x20;       email,

&#x20;       password



&#x20;   FROM admin

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### GET USER BY EMAIL (URSAFE\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserByEmail(IN p\_email VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       lastname,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email,

&#x20;       password



&#x20;   FROM users

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### GET USER BY ID (URSAFE\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserById(IN p\_id VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       id,



&#x20;       TRIM(CONCAT(

&#x20;           firstname, ' ',

&#x20;           IFNULL(CONCAT(middlename, ' '), ''),

&#x20;           lastname

&#x20;       )) AS fullname,



&#x20;       lastname,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email,

&#x20;       password



&#x20;   FROM users

&#x20;   WHERE id = p\_id

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### GET CAMPUS STUDENTS BY EMAIL (CAMPUS\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE getCampusStudentByEmail(IN p\_email VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       id,



&#x20;       TRIM(CONCAT(

&#x20;           firstname, ' ',

&#x20;           IFNULL(CONCAT(middlename, ' '), ''),

&#x20;           lastname

&#x20;       )) AS fullname,



&#x20;       lastname,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       email



&#x20;   FROM students

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### ACTIVATE USER ACCOUNT (URSAFE\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE activateUserAccount(

&#x20;   IN p\_id VARCHAR(255),

&#x20;   IN p\_lastname VARCHAR(255),

&#x20;   IN p\_firstname VARCHAR(255),

&#x20;   IN p\_middlename VARCHAR(255),

&#x20;   IN p\_sex VARCHAR(6),

&#x20;   IN p\_dob DATE,

&#x20;   IN p\_institute VARCHAR(255),

&#x20;   IN p\_program VARCHAR(255),

&#x20;   IN p\_username VARCHAR(255),

&#x20;   IN p\_email VARCHAR(255),

&#x20;   IN p\_password VARCHAR(255)

)

BEGIN

&#x20;   INSERT INTO users (

&#x20;       id,

&#x20;       lastname,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email,

&#x20;       password

&#x20;   )

&#x20;   VALUES (

&#x20;       p\_id,

&#x20;       p\_lastname,

&#x20;       p\_firstname,

&#x20;       p\_middlename,

&#x20;       p\_sex,

&#x20;       p\_dob,

&#x20;       p\_institute,

&#x20;       p\_program,

&#x20;       p\_username,

&#x20;       p\_email,

&#x20;       p\_password

&#x20;   );

ORDER BY id DESC;



END //



DELIMITER ;



##### UPDATE USER PASSWORD (URSAFE\_DB)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateUserPassword(

&#x20;   IN p\_email VARCHAR(255),

&#x20;   IN p\_password VARCHAR(255)

)

BEGIN

&#x20;   UPDATE users

&#x20;   SET password = p\_password

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



# RESET AI (URSAFE\_DB)



ALTER TABLE locker\_applications AUTO\_INCREMENT = 1;

ALTER TABLE locker\_locations AUTO\_INCREMENT = 1;

ALTER TABLE locker\_logs AUTO\_INCREMENT = 1;

ALTER TABLE locker\_sizes AUTO\_INCREMENT = 1;

ALTER TABLE locker\_slots AUTO\_INCREMENT = 1;

ALTER TABLE user\_account\_logs AUTO\_INCREMENT = 1;

