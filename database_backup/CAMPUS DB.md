# CAMPUS DB



##### GET CAMPUS STUDENTS BY EMAIL (PROCEDURE)



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



##### GET STUDENTS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_students\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS students\_count

&#x20;   FROM students;

END //



DELIMITER ;

