# USERS (PROCEDURES)



##### GET USERS



DELIMITER //



CREATE OR REPLACE PROCEDURE getUsers()

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x09;firstname,

&#x09;middlename,

&#x09;lastname,

&#x20;       TRIM(CONCAT(firstname, ' ', IFNULL(CONCAT(middlename, ' '), ''), lastname)) AS fullname,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email

&#x20;   FROM users

&#x20;   ORDER BY lastname ASC;

END //



DELIMITER ;



##### GET SEARCH AND FILTER USERS



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterUsers (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN sortOrder VARCHAR(255)

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       lastname,

&#x20;       TRIM(CONCAT(firstname, ' ',

&#x20;           IFNULL(CONCAT(middlename, ' '), ''),

&#x20;           lastname

&#x20;       )) AS fullname,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email

&#x20;   FROM users

&#x20;   WHERE (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR TRIM(CONCAT(firstname, ' ',

&#x20;           IFNULL(CONCAT(middlename, ' '), ''),

&#x20;           lastname

&#x20;       )) LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN sortOrder = 'desc' THEN TRIM(CONCAT(firstname, ' ',

&#x20;               IFNULL(CONCAT(middlename, ' '), ''),

&#x20;               lastname))

&#x20;       END DESC,



&#x20;       CASE

&#x20;           WHEN sortOrder = 'asc'

&#x20;             OR sortOrder = ''

&#x20;             OR sortOrder IS NULL THEN TRIM(CONCAT(firstname, ' ',

&#x20;               IFNULL(CONCAT(middlename, ' '), ''),

&#x20;               lastname))

&#x20;       END ASC;

END //



DELIMITER ;



##### GET USER ACCOUNT LOGS



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserAccountLogs(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x09;action,

&#x20;       description,

&#x20;       created\_at

&#x20;   FROM user\_account\_logs

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_offset, p\_limit;

END //



DELIMITER ;



# USERS (TRIGGERS)



##### AFTER USER ACTIVATION



DELIMITER //



CREATE OR REPLACE TRIGGER after\_user\_activation

AFTER INSERT ON users

FOR EACH ROW



BEGIN

&#x09;DECLARE action VARCHAR(255) DEFAULT 'Activated';

&#x20;   	DECLARE description VARCHAR(255);



&#x09;SET description = CONCAT(NEW.username, ' (', '#', NEW.id, ') ', 'account has been activated.');

&#x20;

&#x09;INSERT INTO user\_account\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER USER DELETION



DELIMITER //



CREATE OR REPLACE TRIGGER after\_user\_deletion

AFTER DELETE ON users

FOR EACH ROW



BEGIN

&#x09;DECLARE action VARCHAR(255) DEFAULT 'Deleted';

&#x20;  	DECLARE description VARCHAR(255);



&#x09;SET description = CONCAT(OLD.username, ' (', '#', OLD.id, ') ', 'account has been deleted.');

&#x20;

&#x09;INSERT INTO user\_account\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;

