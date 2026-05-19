# URSAFE DB



##### GET ADMIN BY EMAIL (PROCEDURE)



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



##### GET USER BY EMAIL (PROCEDURE)



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



##### GET USER BY ID (PROCEDURE)



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



##### ACTIVATE USER ACCOUNT (PROCEDURE)



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



END //



DELIMITER ;



##### UPDATE USER PASSWORD (PROCEDURE)



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



##### GET USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUsers(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       lastname,

&#x20;       TRIM(CONCAT(firstname, ' ', IFNULL(CONCAT(middlename, ' '), ''), lastname)) AS fullname,

&#x20;       sex,

&#x20;       DATE\_FORMAT(dob, '%M %d, %Y') AS dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM users

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS usersTotal

&#x20;   FROM users;



END //



DELIMITER ;

&#x20;

##### GET SEARCH AND FILTER USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterUsers (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN sortOrder VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       lastname,

&#x20;       TRIM(CONCAT(

&#x20;           firstname, ' ',

&#x20;           IFNULL(CONCAT(middlename, ' '), ''),

&#x20;           lastname

&#x20;       )) AS fullname,

&#x20;       sex,

&#x20;       DATE\_FORMAT(dob, '%M %d, %Y') AS dob,

&#x20;       institute,

&#x20;       program,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM users



&#x20;   WHERE

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')



&#x20;   ORDER BY

&#x20;       CASE WHEN sortOrder = 'a-z' THEN firstname END ASC,

&#x20;       CASE WHEN sortOrder = 'z-a' THEN firstname END DESC,

&#x20;       CASE WHEN sortOrder = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN sortOrder = 'newest' THEN created\_at END DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS usersTotal

&#x20;   FROM users

&#x20;   WHERE

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%');



END //



DELIMITER ;



##### GET USER ACCOUNT LOGS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserAccountLogs(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       action,

&#x20;       description,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM user\_account\_logs

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_offset, p\_limit;



&#x20;   SELECT COUNT(\*) AS userLogsTotal

&#x20;   FROM user\_account\_logs;



END //



DELIMITER ;



##### GET USERS COUNT



DELIMITER //



CREATE PROCEDURE get\_users\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS users\_count

&#x20;   FROM users;

END //



DELIMITER ;



##### GET RECENT USER ACCOUNT ACTIVATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_recent\_user\_account\_activation()

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       username,

&#x20;       lastname,

&#x09;firstname,

&#x09;middlename,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM recent\_user\_account\_activation;

END //



DELIMITER ;



##### AFTER USER ACTIVATION (TRIGGER)



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



##### AFTER USER DELETION (TRIGGER)



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



##### GET RECENT USER ACCOUNT ACTIVATION (VIEW)



CREATE OR REPLACE VIEW recent\_user\_account\_activation AS



SELECT

&#x20;   id,

&#x20;   lastname,

&#x20;   firstname,

&#x20;   middlename,

&#x20;   created\_at

FROM users

ORDER BY created\_at DESC

LIMIT 1;



##### GET PENDING LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getPendingLockerApplications(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       ls.size\_id,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status = 'Pending'

&#x20;   ORDER BY la.created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS pendingTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Pending';



END//



DELIMITER ;



##### GET SEARCH AND FILTER PENDING LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterPendingLockerApplications(

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status = 'Pending'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.created\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.created\_at END ASC,

&#x20;       la.created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS pendingTotal

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   WHERE la.status = 'Pending'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### GET ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAcceptedLockerApplications(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       ls.size\_id,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status = 'Accepted'

&#x20;   ORDER BY la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS acceptedTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Accepted';



END//



DELIMITER ;



##### GET SEARCH AND FILTER ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterAcceptedLockerApplications(

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status = 'Accepted'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS acceptedTotal

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   WHERE la.status = 'Accepted'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### GET LOCKER APPLICATIONS HISTORY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerApplicationHistory(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       ls.size\_id,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')

&#x20;   ORDER BY la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS historyTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');



END//



DELIMITER ;



##### GET SEARCH AND FILTER LOCKER APPLICATIONS HISTORY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerApplicationHistory(

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS historyTotal

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');



END//



DELIMITER ;



##### GET LOCKER LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLocations()

BEGIN

&#x20;   SELECT id,

&#x09;location,

&#x09;DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x09;FROM locker\_locations

&#x20;   ORDER BY created\_at DESC;

END //



DELIMITER ;



##### GET SEARCH AND FILTER LOCKER LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerLocation (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255)

)

BEGIN

&#x20;   SELECT id,

&#x20;   location,

&#x20;   DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM locker\_locations



&#x20;   WHERE

&#x20;   	searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR location LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN filterTerm = 'a-z' THEN location

&#x20;       END ASC,

&#x20;

&#x20;       CASE

&#x20;           WHEN filterTerm = 'z-a' THEN location

&#x20;       END DESC,

&#x20;

&#x20;       CASE

&#x20;           WHEN filterTerm = 'oldest' THEN created\_at

&#x20;       END ASC,

&#x20;

&#x20;       CASE

&#x20;           WHEN filterTerm = 'newest' THEN created\_at

&#x20;       END DESC;



END //



DELIMITER ;



##### ADD LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerLocation (

&#x20;   IN p\_location VARCHAR(255)

)

BEGIN

&#x20;   INSERT INTO locker\_locations (location)

&#x20;   VALUES (p\_location);

END //



DELIMITER ;



##### UPDATE LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLockerLocation (

&#x20;   IN p\_id INT,

&#x20;   IN p\_location VARCHAR(255)

)

BEGIN

&#x20;   UPDATE locker\_locations

&#x20;   SET location = p\_location

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### DELETE LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerLocation (

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_locations

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### GET LOCKER LOCATION LOGS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLogs(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       action,

&#x20;       description,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM locker\_logs

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_offset, p\_limit;



&#x20;   SELECT COUNT(\*) AS lockerLogsTotal

&#x20;   FROM locker\_logs;



END //



DELIMITER ;



##### GET LOCKER SIZES (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerSizes()

BEGIN

&#x20;   SELECT id,

&#x20;       size,

&#x20;       price,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;       FROM locker\_sizes

&#x20;   ORDER BY created\_at DESC;

END //



DELIMITER ;



##### GET SEARCH AND FILTER LOCKER SIZES (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerSizes (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255)

)

BEGIN



&#x20;   SELECT

&#x20;       id,

&#x20;       size,

&#x20;       price,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM locker\_sizes



&#x20;   WHERE (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR price LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN filterTerm = 'a-z' THEN size

&#x20;       END ASC,



&#x20;       CASE

&#x20;           WHEN filterTerm = 'z-a' THEN size

&#x20;       END DESC,



&#x20;       CASE

&#x20;           WHEN filterTerm = 'oldest' THEN created\_at

&#x20;       END ASC,



&#x20;       CASE

&#x20;           WHEN filterTerm = 'newest' THEN created\_at

&#x20;       END DESC,



&#x20;       CASE

&#x20;           WHEN filterTerm = 'cheaper' THEN price

&#x20;       END ASC,



&#x20;       CASE

&#x20;           WHEN filterTerm = 'expensive' THEN price

&#x20;       END DESC;



END //



DELIMITER ;



##### ADD LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerSize (

&#x20;   IN p\_size VARCHAR(255),

&#x20;   IN p\_price DOUBLE(10,2)

)

BEGIN

&#x20;   INSERT INTO locker\_sizes (size, price)

&#x20;   VALUES (p\_size, p\_price);

END //



DELIMITER ;



##### UPDATE LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLockerSize(

&#x20;   IN p\_id INT,

&#x20;   IN p\_size VARCHAR(255),

&#x20;   IN p\_price DOUBLE

)

BEGIN

&#x20;   UPDATE locker\_sizes

&#x20;   SET size = p\_size,

&#x20;       price = p\_price

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### DELETE LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerSize(

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_sizes

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### GET LOCKERS BY LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockersByLocation (

&#x20;   IN p\_location\_id INT

)

BEGIN

&#x20;   SELECT

&#x20;       ls.id,

&#x20;       ls.slot\_number,

&#x20;       lsz.size,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       lsz.price,

&#x20;       ls.status

&#x20;   FROM locker\_slots ls

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE ls.location\_id = p\_location\_id

&#x20;   ORDER BY ls.slot\_number ASC;

END //



DELIMITER ;



##### GET USER LOCKER APPLICATION ID (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserLockerApplicationId (

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   SELECT

&#x20;       slot\_id,

&#x20;       user\_id

&#x20;   FROM locker\_applications

&#x20;   WHERE id = p\_application\_id

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### GET USER LOCKER APPLICATION DETAILS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserLockerApplicationDetails (

&#x20;   IN p\_slot\_id INT

)

BEGIN

&#x20;   SELECT

&#x20;       ll.location,

&#x20;       ls.slot\_number,

&#x20;       sz.size,

&#x20;       sz.price

&#x20;   FROM locker\_slots ls

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE ls.id = p\_slot\_id

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### ADD LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLocker (

&#x20;   IN p\_slot\_number INT,

&#x20;   IN p\_location\_id INT,

&#x20;   IN p\_size\_id INT,

&#x20;   IN p\_start\_at DATE,

&#x20;   IN p\_end\_at DATE

)

BEGIN

&#x20;   INSERT INTO locker\_slots (

&#x20;       slot\_number,

&#x20;       location\_id,

&#x20;       size\_id,

&#x20;       start\_at,

&#x20;       end\_at,

&#x20;       status

&#x20;   )

&#x20;   VALUES (

&#x20;       p\_slot\_number,

&#x20;       p\_location\_id,

&#x20;       p\_size\_id,

&#x20;       p\_start\_at,

&#x20;       p\_end\_at,

&#x20;       'Available'

&#x20;   );

END //



DELIMITER ;



##### UPDATE LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLocker (

&#x20;   IN p\_id INT,

&#x20;   IN p\_slot\_number INT,

&#x09;IN p\_start\_date DATE,

&#x20;   IN p\_end\_date DATE,

&#x20;   IN p\_status VARCHAR(255)

)

BEGIN

&#x20;   UPDATE locker\_slots

&#x20;   SET

&#x20;       slot\_number = p\_slot\_number,

&#x20;       start\_at = p\_start\_date,

&#x20;       end\_at = p\_end\_date,

&#x20;       status = p\_status,

&#x20;       updated\_at = NOW()

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### DELETE LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLocker (

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_slots

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### ACCEPT LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE acceptLockerApplication(

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   DECLARE v\_slot\_id INT;

&#x20;   DECLARE v\_slot\_status VARCHAR(50);



&#x20;   START TRANSACTION;



&#x20;   SELECT slot\_id

&#x20;   INTO v\_slot\_id

&#x20;   FROM locker\_applications

&#x20;   WHERE id = p\_application\_id;



&#x20;   SELECT status

&#x20;   INTO v\_slot\_status

&#x20;   FROM locker\_slots

&#x20;   WHERE id = v\_slot\_id;



&#x20;   IF v\_slot\_status IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Slot not found';



&#x20;   ELSEIF v\_slot\_status <> 'Available' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Slot is no longer available';

&#x20;   END IF;



&#x20;   UPDATE locker\_applications

&#x20;   SET status = 'Accepted', updated\_at = NOW()

&#x20;   WHERE id = p\_application\_id;



&#x20;   UPDATE locker\_slots

&#x20;   SET status = 'Occupied'

&#x20;   WHERE id = v\_slot\_id;



&#x20;   COMMIT;

END //



DELIMITER ;



##### REJECT LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE rejectLockerApplication(

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   DECLARE v\_status VARCHAR(50);



&#x20;   SELECT status

&#x20;   INTO v\_status

&#x20;   FROM locker\_applications

&#x20;   WHERE id = p\_application\_id;



&#x20;   IF v\_status IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Application not found';



&#x20;   ELSEIF v\_status <> 'Pending' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Only pending applications can be rejected';

&#x20;   END IF;



&#x20;   UPDATE locker\_applications

&#x20;   SET status = 'Rejected', updated\_at = NOW()

&#x20;   WHERE id = p\_application\_id;



END //



DELIMITER ;



##### REVOKE LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE revokeLockerApplication(

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   DECLARE v\_slot\_id INT;

&#x20;   DECLARE v\_status VARCHAR(50);



&#x20;   START TRANSACTION;



&#x20;   SELECT slot\_id, status

&#x20;   INTO v\_slot\_id, v\_status

&#x20;   FROM locker\_applications

&#x20;   WHERE id = p\_application\_id;



&#x20;   IF v\_status IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Application not found';



&#x20;   ELSEIF v\_status <> 'Accepted' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Only accepted applications can be revoked';

&#x20;   END IF;



&#x20;   UPDATE locker\_applications

&#x20;   SET status = 'Revoked', updated\_at = NOW()

&#x20;   WHERE id = p\_application\_id;



&#x20;   UPDATE locker\_slots

&#x20;   SET status = 'Available'

&#x20;   WHERE id = v\_slot\_id;



&#x20;   COMMIT;



END //



DELIMITER ;



##### END LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE endLockerApplication()

BEGIN

&#x20;   UPDATE locker\_slots SET status = 'Available'

&#x20;   WHERE end\_at <= CURDATE() AND status = 'Occupied';



&#x20;   UPDATE locker\_applications la

&#x20;   JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   SET la.status = 'Ended', la.updated\_at = NOW()

&#x20;   WHERE ls.end\_at <= CURDATE() AND la.status IN ('Pending', 'Accepted');

END //



DELIMITER ;



##### GET MY ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyAcceptedLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status = 'Accepted'

&#x20;   ORDER BY la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myAcceptedTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id

&#x20;     AND status = 'Accepted';



END //



DELIMITER ;



##### GET MY PENDING LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyPendingLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status = 'Pending'

&#x20;   ORDER BY la.created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myPendingTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id

&#x20;     AND status = 'Pending';



END //



DELIMITER ;



##### GET MY LOCKER APPLICATION HISTORY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyLockerApplicationHistory(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       DATE\_FORMAT(ls.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ls.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')

&#x20;   ORDER BY la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myHistoryTotal

&#x20;   FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id

&#x20;     AND status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');



END //



DELIMITER ;



##### APPLY LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE applyLocker (

&#x20;   IN u\_user\_id VARCHAR(255),

&#x20;   IN u\_slot\_id INT

)

BEGIN

&#x20;   DECLARE active\_application\_count INT DEFAULT 0;

&#x20;   DECLARE slot\_status VARCHAR(50);

&#x20;   DECLARE v\_start\_at DATE;

&#x20;   DECLARE v\_end\_at DATE;

&#x20;   DECLARE slot\_exists INT DEFAULT 1;



&#x20;   DECLARE CONTINUE HANDLER FOR NOT FOUND SET slot\_exists = 0;



&#x20;   SELECT status, start\_at, end\_at

&#x20;   INTO slot\_status, v\_start\_at, v\_end\_at

&#x20;   FROM locker\_slots

&#x20;   WHERE id = u\_slot\_id;



&#x20;   IF slot\_exists = 0 THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Slot not found';

&#x20;   END IF;



&#x20;   IF slot\_status <> 'Available' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'This slot is not available for application';

&#x20;   END IF;



&#x20;   IF CURDATE() < v\_start\_at THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Application has not started yet';



&#x20;   ELSEIF CURDATE() >= v\_end\_at THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Application period has ended';

&#x20;   END IF;



&#x20;   SELECT COUNT(\*)

&#x20;   INTO active\_application\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE user\_id = u\_user\_id

&#x20;     AND slot\_id = u\_slot\_id

&#x20;     AND status IN ('Pending', 'Accepted');



&#x20;   IF active\_application\_count > 0 THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'You already applied for this slot';



&#x20;   ELSE

&#x20;       INSERT INTO locker\_applications (

&#x20;           user\_id,

&#x20;           slot\_id,

&#x20;           status

&#x20;       )

&#x20;       VALUES (

&#x20;           u\_user\_id,

&#x20;           u\_slot\_id,

&#x20;           'Pending'

&#x20;       );

&#x20;   END IF;



END //



DELIMITER ;



##### CANCEL LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE cancelLockerApplication (

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   UPDATE locker\_applications

&#x20;   SET status = 'Cancelled', updated\_at = NOW()

&#x20;   WHERE id = p\_application\_id

&#x20;     AND status = 'Pending';

END //



DELIMITER ;



##### GET TOTAL LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_total\_lockers\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS total\_lockers\_count

&#x20;   FROM locker\_slots;

END //



DELIMITER ;



##### GET AVAILABLE LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_available\_lockers\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS available\_lockers\_count

&#x20;   FROM locker\_slots

&#x20;   WHERE status = 'Available';

END //



DELIMITER ;



##### GET OCCUPIED LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_occupied\_lockers\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS occupied\_lockers\_count

&#x20;   FROM locker\_slots

&#x20;   WHERE status = 'Occupied';

END //



DELIMITER ;



##### GET TOTAL LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_total\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS total\_locker\_applications\_count

&#x20;   FROM locker\_applications;

END //



DELIMITER ;



##### GET PENDING LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_pending\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS pending\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Pending';

END //



DELIMITER ;



##### GET CANCELLED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_cancelled\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS cancelled\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Cancelled';

END //



DELIMITER ;



##### GET ACCEPTED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_accepted\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS accepted\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Accepted';

END //



DELIMITER ;



##### GET REJECTED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_rejected\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS rejected\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Rejected';

END //



DELIMITER ;



##### GET REVOKED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_revoked\_locker\_applications\_count()

BEGIN

&#x20;   SELECT COUNT(\*) AS revoked\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Revoked';

END //



DELIMITER ;



##### GET RECENT LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE get\_recent\_locker\_application()

BEGIN

&#x20;   SELECT

&#x20;   	id,

&#x20;       user\_id,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       lastname,

&#x20;       location,

&#x20;       slot\_number,

&#x20;       size,

&#x20;       price,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM recent\_locker\_application;

END //



DELIMITER ;



##### AFTER LOCKER LOCATION INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_insertion

AFTER INSERT ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Addition';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Location ', NEW.location, ' has been added.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER SIZES INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_insertion

AFTER INSERT ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Addition';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Size ', NEW.size, ' with price ₱', NEW.price, ' has been added.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER SLOT INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_insertion

AFTER INSERT ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Addition';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Slot #', NEW.slot\_number, ' has been added (Location ID: ', NEW.location\_id, ', Size ID: ', NEW.size\_id, ').');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER LOCATION UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_updation

AFTER UPDATE ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Updation';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Location ', OLD.location, ' has been updated to ', NEW.location, '.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER SIZES UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_updation

AFTER UPDATE ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Updation';

&#x20;   DECLARE description VARCHAR(255);



&#x09;IF OLD.size <> NEW.size THEN

&#x09;	SET description = CONCAT('Size ', OLD.size, ' has been updated to ', NEW.size, '.');

&#x09;ELSEIF OLD.price <> NEW.price THEN

&#x09;	SET description = CONCAT('Price ', OLD.price, ' has been updated to ', NEW.price, ' on ', NEW.size, '.');

&#x09;END IF;



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### AFTER LOCKER SLOT UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_updation

AFTER UPDATE ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Updation';

&#x20;   DECLARE description VARCHAR(255) DEFAULT 'Locker updated.';



&#x20;   IF OLD.slot\_number <> NEW.slot\_number THEN



&#x20;       SET description = CONCAT(

&#x20;           'Slot #',

&#x20;           OLD.slot\_number,

&#x20;           ' has been updated to Slot #',

&#x20;           NEW.slot\_number,

&#x20;           '.'

&#x20;       );



&#x20;   ELSEIF OLD.status <> NEW.status THEN



&#x20;       SET description = CONCAT(

&#x20;           'Slot #',

&#x20;           NEW.slot\_number,

&#x20;           ' status of ',

&#x20;           OLD.status,

&#x20;           ' has been updated to ',

&#x20;           NEW.status,

&#x20;           '.'

&#x20;       );



&#x20;   END IF;



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //





DELIMITER ;



##### AFTER LOCKER LOCATION DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_deletion

AFTER DELETE ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Deletion';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Location ', OLD.location, ' has been deleted.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER SIZES DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_deletion

AFTER DELETE ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Deletion';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Size ', OLD.size, ' with price ₱', OLD.price, ' has been deleted.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER SLOT DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_deletion

AFTER DELETE ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Deletion';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Slot #', OLD.slot\_number, ' has been deleted (Location ID: ', OLD.location\_id, ', Size ID: ', OLD.size\_id, ').');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### AFTER LOCKER APPLICATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_application

AFTER INSERT ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Application';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   WHERE ls.id = NEW.slot\_id

&#x20;   LIMIT 1;



&#x20;   SET description = CONCAT(NEW.user\_id, ' has applied on slot #', v\_slot\_number, ' at ', v\_location, '.');



&#x20;   INSERT INTO locker\_logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### AFTER LOCKER CANCELLATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_cancellation

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Cancellation';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x09;IF NEW.status = 'Cancelled' AND OLD.status <> 'Cancelled' THEN

&#x20;       SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(NEW.user\_id, ' has cancelled slot #', v\_slot\_number, ' at ', v\_location, '.');



&#x20;       INSERT INTO locker\_logs (

&#x20;           action,

&#x20;           description

&#x20;       ) VALUES (

&#x20;           action,

&#x20;           description

&#x20;       );

&#x09;END IF;



END //



DELIMITER ;



##### AFTER LOCKER ACCEPTATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_acception

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Acception';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x09;IF NEW.status = 'Accepted' AND OLD.status <> 'Accepted' THEN

&#x20;       SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(NEW.user\_id, ' application on slot #', v\_slot\_number, ' at ', v\_location, ' has been accepted.');



&#x20;       INSERT INTO locker\_logs (

&#x20;           action,

&#x20;           description

&#x20;       ) VALUES (

&#x20;           action,

&#x20;           description

&#x20;       );

&#x09;END IF;



END //



DELIMITER ;



##### AFTER LOCKER REJECTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_rejection

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Rejection';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x09;IF NEW.status = 'Rejected' AND OLD.status <> 'Rejected' THEN

&#x20;       SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(NEW.user\_id, ' application on slot #', v\_slot\_number, ' at ', v\_location, ' has been rejected.');



&#x20;       INSERT INTO locker\_logs (

&#x20;           action,

&#x20;           description

&#x20;       ) VALUES (

&#x20;           action,

&#x20;           description

&#x20;       );

&#x09;END IF;



END //



DELIMITER ;



##### AFTER LOCKER REVOKING (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_revoking

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Revoking';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x09;IF NEW.status = 'Revoked' AND OLD.status <> 'Revoked' THEN

&#x20;       SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(NEW.user\_id, ' application on slot #', v\_slot\_number, ' at ', v\_location, ' has been revoked.');



&#x20;       INSERT INTO locker\_logs (

&#x20;           action,

&#x20;           description

&#x20;       ) VALUES (

&#x20;           action,

&#x20;           description

&#x20;       );

&#x09;END IF;



END //



DELIMITER ;



##### AUTO END LOCKER (EVENT)



DELIMITER //



CREATE OR REPLACE EVENT auto\_end\_locker

ON SCHEDULE EVERY 1 DAY

STARTS CURRENT\_TIMESTAMP

DO

BEGIN

&#x20;   CALL endLockerApplication();

END//



DELIMITER ;



##### GET RECENT LOCKER APPLICATION (VIEW)



CREATE OR REPLACE VIEW recent\_locker\_application AS



SELECT

&#x20;   la.id,

&#x20;   la.user\_id,

&#x20;   u.firstname,

&#x20;   u.middlename,

&#x20;   u.lastname,

&#x20;   ll.location,

&#x20;   ls.slot\_number,

&#x20;   lsz.size,

&#x20;   lsz.price,

&#x20;   la.created\_at

FROM locker\_applications la



INNER JOIN users u ON la.user\_id = u.id

INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id



WHERE la.status = 'Pending'

ORDER BY la.created\_at DESC

LIMIT 1;

