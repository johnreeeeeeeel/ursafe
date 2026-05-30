# \-- URSAFE DB



##### \-- GET ADMIN BY EMAIL (PROCEDURE)



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



##### \-- GET USER BY EMAIL (PROCEDURE)



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



##### \-- GET USER BY ID (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserById(

&#x20;   IN p\_value VARCHAR(255)

)

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

&#x20;   WHERE id = p\_value

&#x20;      OR username = p\_value

&#x20;   LIMIT 1;



END //



DELIMITER ;



##### \-- VALIDATE AND ACTIVATE USER ACCOUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE validateAndActivateUserAccount(

&#x20;   IN p\_email VARCHAR(255),

&#x20;   IN p\_username VARCHAR(255),

&#x20;   IN p\_password VARCHAR(255)

)



BEGIN

&#x20;   DECLARE v\_status VARCHAR(255);

&#x20;   DECLARE v\_email VARCHAR(255);

&#x20;   DECLARE v\_username\_count INT DEFAULT 0;



&#x20;   SELECT status, email

&#x20;   INTO v\_status, v\_email

&#x20;   FROM users

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;



&#x20;   IF v\_email IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'EMAIL\_NOT\_FOUND';

&#x20;   END IF;



&#x20;   IF v\_status = 'Active' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'ACCOUNT\_ALREADY\_ACTIVE';

&#x20;   END IF;



&#x20;   SELECT COUNT(\*) INTO v\_username\_count

&#x20;   FROM users

&#x20;   WHERE username = p\_username;



&#x20;   IF v\_username\_count > 0 THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'USERNAME\_EXISTS';

&#x20;   END IF;



&#x20;   UPDATE users

&#x20;   SET

&#x20;       username = p\_username,

&#x20;       password = p\_password,

&#x20;       status = 'Active',

&#x20;       updated\_at = NOW()

&#x20;   WHERE email = p\_email;



&#x20;   SELECT

&#x20;       id,

&#x20;       lastname,

&#x20;       firstname,

&#x20;       middlename,

&#x20;       sex,

&#x20;       dob,

&#x20;       institute,

&#x20;       program,

&#x20;       status,

&#x20;       username,

&#x20;       email,

&#x20;       created\_at,

&#x20;       updated\_at

&#x20;   FROM users

&#x20;   WHERE email = p\_email

&#x20;   LIMIT 1;



END //



DELIMITER ;



##### \-- VALIDATE LOGIN (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE validateLogin (

&#x20;   IN p\_email VARCHAR(255)

)



BEGIN

&#x20;   DECLARE v\_status VARCHAR(255);



&#x20;   -- Check admin

&#x20;   IF EXISTS (

&#x20;       SELECT 1 FROM admin WHERE email = p\_email

&#x20;

&#x20;   ) THEN

&#x20;       SELECT NULL INTO v\_status;

&#x20;       SELECT

&#x20;           id,

&#x20;           NULL AS lastname,

&#x20;           NULL AS firstname,

&#x20;           NULL AS middlename,

&#x20;           NULL AS sex,

&#x20;           NULL AS dob,

&#x20;           NULL AS institute,

&#x20;           NULL AS program,

&#x20;           username,

&#x20;           email,

&#x20;           password,

&#x20;           'admin' AS role

&#x20;       FROM admin

&#x20;       WHERE email = p\_email

&#x20;       LIMIT 1;



&#x20;   -- Check user

&#x20;   ELSEIF EXISTS (

&#x20;       SELECT 1 FROM users WHERE email = p\_email

&#x20;

&#x20;   ) THEN

&#x20;       SELECT status INTO v\_status

&#x20;       FROM users WHERE email = p\_email LIMIT 1;



&#x20;       IF v\_status = 'Inactive' THEN

&#x20;           SIGNAL SQLSTATE '45000'

&#x20;           SET MESSAGE\_TEXT = 'Account is inactive. Please activate your account first.';

&#x20;       END IF;



&#x20;       SELECT

&#x20;           id,

&#x20;           lastname,

&#x20;           firstname,

&#x20;           middlename,

&#x20;           sex,

&#x20;           dob,

&#x20;           institute,

&#x20;           program,

&#x20;           username,

&#x20;           email,

&#x20;           password,

&#x20;           'user' AS role

&#x20;       FROM users

&#x20;       WHERE email = p\_email

&#x20;       LIMIT 1;



&#x20;   ELSE

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Account not found';

&#x20;   END IF;



END //



DELIMITER ;



##### \-- UPDATE USER PASSWORD (PROCEDURE)



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



##### \-- GET ACTIVE USER COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getActiveUserCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS active\_user\_count

&#x20;   FROM users WHERE status = 'Active';

END //



DELIMITER ;



##### \-- GET ACTIVE USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getActiveUsers(

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

&#x20;       status,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM users WHERE status = 'Active'

&#x20;

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS activeUsersTotal

&#x20;   FROM users WHERE status = 'Active';



END //



DELIMITER ;

&#x20;

##### \-- GET SEARCH AND FILTER ACTIVE USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterActiveUsers (

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

&#x20;       status,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM users

&#x20;   WHERE status = 'Active'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN sortOrder = 'a-z' THEN firstname END ASC,

&#x20;       CASE WHEN sortOrder = 'z-a' THEN firstname END DESC,

&#x20;       CASE WHEN sortOrder = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN sortOrder = 'newest' THEN created\_at END DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS activeUsersTotal

&#x20;   FROM users

&#x20;   WHERE status = 'Active'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- GET INACTIVE USER COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getInactiveUserCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS inactive\_user\_count

&#x20;   FROM users WHERE status = 'Inactive';

END //



DELIMITER ;



##### \-- GET INACTIVE USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getInactiveUsers(

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

&#x20;       status,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM users WHERE status = 'Inactive'

&#x20;

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS inactiveUsersTotal

&#x20;   FROM users WHERE status = 'Inactive';



END //



DELIMITER ;

&#x20;

##### \-- GET SEARCH AND FILTER INACTIVE USERS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterInactiveUsers (

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

&#x20;       status,

&#x20;       username,

&#x20;       email,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM users

&#x20;   WHERE status = 'Inactive'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN sortOrder = 'a-z' THEN firstname END ASC,

&#x20;       CASE WHEN sortOrder = 'z-a' THEN firstname END DESC,

&#x20;       CASE WHEN sortOrder = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN sortOrder = 'newest' THEN created\_at END DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS inactiveUsersTotal

&#x20;   FROM users

&#x20;   WHERE status = 'Inactive'

&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR email LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER USER ACTIVATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_user\_activation

AFTER UPDATE ON users

FOR EACH ROW



BEGIN

&#x20;    IF OLD.status <> 'Active' AND NEW.status = 'Active' THEN



&#x20;       INSERT INTO logs (

&#x20;           action,

&#x20;           description

&#x20;       )

&#x20;       VALUES (

&#x20;           'Activate',

&#x20;           CONCAT(

&#x20;               NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''), ' | ', NEW.id, ' | ', NEW.email, ' account has been activated.'

&#x20;           )

&#x20;       );



&#x20;   END IF;



END //



DELIMITER ;



##### \-- AFTER USER DEACTIVATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_user\_deactivation

AFTER UPDATE ON users

FOR EACH ROW



BEGIN

&#x20;   IF OLD.status = 'Active' AND NEW.status = 'Inactive' THEN



&#x20;       INSERT INTO logs (

&#x20;           action,

&#x20;           description

&#x20;       )

&#x20;       VALUES (

&#x20;           'Deactivate',

&#x20;           CONCAT(

&#x20;                NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''), ' | ', NEW.id, ' | ', NEW.email, ' account has been deactivated.'            

&#x09;    )

&#x20;       );



&#x20;   END IF;



END //



DELIMITER ;



##### \-- GET RECENT USER ACCOUNT ACTIVATION (VIEW)



CREATE OR REPLACE VIEW recent\_user\_account\_activation AS



SELECT

&#x20;   id,

&#x20;   lastname,

&#x20;   firstname,

&#x20;   middlename,

&#x20;   username,

&#x20;   created\_at

FROM users

WHERE status = 'Active'

ORDER BY created\_at DESC

LIMIT 1;



##### \-- GET RECENT USER ACCOUNT ACTIVATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getRecentUserAccountActivation()

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       username,

&#x20;       lastname,

&#x09;firstname,

&#x09;middlename,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM recent\_user\_account\_activation;

END //



DELIMITER ;



##### \-- GET RECENT LOCKER APPLICATION (VIEW)



CREATE OR REPLACE VIEW recent\_locker\_application AS



SELECT

&#x20;   la.id,

&#x20;   la.user\_id,

&#x20;   ls.slot\_number,

&#x20;   ll.location,

&#x20;   lsz.size,

&#x20;   lsz.price,

&#x20;   la.status,

&#x20;   la.payment,

&#x20;   la.created\_at

FROM locker\_applications la



INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id



WHERE la.status = 'Pending'

ORDER BY la.created\_at DESC

LIMIT 1;



##### \-- GET RECENT LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getRecentLockerApplication()

BEGIN

&#x20;   SELECT

&#x20;   	id,

&#x20;       user\_id,

&#x20;       slot\_number,

&#x20;       location,

&#x20;       size,

&#x20;       price,

&#x20;       status,

&#x20;       payment,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM recent\_locker\_application;

END //



DELIMITER ;



##### \-- GET PAID ENDED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getPaidEndedLockerApplicationsCount()

BEGIN

&#x20;   SELECT

&#x20;       COUNT(\*) AS paid\_total\_transactions

&#x20;   FROM locker\_applications

&#x20;   WHERE payment = 'Paid'

&#x20;   AND status = 'Ended';

END //



DELIMITER ;



##### \-- GET PAID ENDED LOCKER APPLICATIONS AMOUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getPaidEndedLockerApplicationsAmount()

BEGIN

&#x20;   SELECT

&#x20;       COALESCE(SUM(lsz.price), 0) AS paid\_total\_amount

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.payment = 'Paid'

&#x20;   AND la.status = 'Ended';

END //



DELIMITER ;



##### \-- GET UNPAID LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUnpaidEndedLockerApplicationsCount()

BEGIN

&#x20;   SELECT

&#x20;       COUNT(\*) AS unpaid\_total\_transactions

&#x20;   FROM locker\_applications

&#x20;   WHERE payment = 'Unpaid'

&#x20;   AND status = 'Ended';

END //



DELIMITER ;



##### \-- GET UNPAID LOCKER APPLICATIONS AMOUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getUnpaidEndedLockerApplicationsAmount()

BEGIN

&#x20;   SELECT

&#x20;       COALESCE(SUM(lsz.price), 0) AS unpaid\_total\_amount

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.payment = 'Unpaid'

&#x20;   AND la.status = 'Ended';

END //



DELIMITER ;



##### \-- GET PENDING LOCKER APPLICATIONS (PROCEDURE)



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

&#x20;       la.payment,



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

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.status = 'Pending'

&#x20;   ORDER BY la.created\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS pendingTotal FROM locker\_applications

&#x20;   WHERE status = 'Pending';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER PENDING LOCKER APPLICATIONS (PROCEDURE)



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

&#x09;	la.payment,

&#x20;

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Pending'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.created\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.created\_at END ASC,

&#x20;       la.created\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS pendingTotal

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Pending'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### \-- GET ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



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

&#x20;       la.payment,



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

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.status = 'Accepted'

&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS acceptedTotal FROM locker\_applications

&#x20;   WHERE status = 'Accepted';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



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

&#x09;	la.payment,

&#x20;

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Accepted'

&#x20;  AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS acceptedTotal

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Accepted'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### \-- GET ENDED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getEndedLockerApplications(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x20;       la.payment,



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

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.status = 'Ended' AND la.payment = 'Unpaid'

&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS endedTotal FROM locker\_applications

&#x20;   WHERE status = 'Ended' AND payment = 'Unpaid';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER ENDED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterEndedLockerApplications(

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

&#x09;	la.payment,

&#x20;

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;      	ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Ended' AND la.payment = 'Unpaid'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS endedTotal

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status = 'Ended' AND payment = 'Unpaid'

&#x20;   AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### \-- GET LOCKER APPLICATIONS HISTORY (PROCEDURE)



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

&#x20;       la.payment,



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

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



INNER JOIN users u ON la.user\_id = u.id

LEFT JOIN locker\_slots ls ON la.slot\_id = ls.id

LEFT JOIN locker\_locations ll ON ls.location\_id = ll.id

LEFT JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended')

&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS historyTotal FROM locker\_applications

&#x20;   WHERE status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended');



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER LOCKER APPLICATIONS HISTORY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerApplicationHistory(

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;  SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.user\_id,

&#x20;       la.slot\_id,

&#x20;       la.status,

&#x09;	la.payment,

&#x20;

&#x20;       TRIM(CONCAT(

&#x20;           u.firstname, ' ',

&#x20;           IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;           u.lastname

&#x20;       )) AS fullname,

&#x20;

&#x20;       u.email,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   LEFT JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   LEFT JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   LEFT JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')

&#x20;     AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   )

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC

&#x20;   LIMIT p\_limit OFFSET p\_offset;





&#x20;   SELECT COUNT(\*) AS historyTotal

&#x20;   FROM locker\_applications la

&#x20;

&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   LEFT JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   LEFT JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   LEFT JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')

&#x20;     AND (

&#x20;       searchTerm IS NULL OR searchTerm = ''

&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.user\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR la.slot\_id LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR u.firstname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.middlename LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.lastname LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.username LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR u.email LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR lsz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END//



DELIMITER ;



##### \-- GET LOCKER LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLocations(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       location,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y %h:%i:%s %p') AS updated\_at

&#x09;FROM locker\_locations

&#x20;   ORDER BY created\_at DESC LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS lockerLocationsTotal

&#x20;   FROM locker\_locations;



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER LOCKER LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerLocation (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       location,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at,

&#x20;       DATE\_FORMAT(updated\_at, '%M %d, %Y %h:%i:%s %p') AS updated\_at

&#x20;   FROM locker\_locations



&#x20;   WHERE

&#x20;   	searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR location LIKE CONCAT('%', searchTerm, '%')

&#x20;

&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'a-z' THEN location END ASC,

&#x20;       CASE WHEN filterTerm = 'z-a' THEN location END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN filterTerm = 'newest' THEN created\_at END DESC

&#x20;

&#x09;LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS lockerLocationsTotal

&#x20;   FROM locker\_locations

&#x09;	WHERE

&#x20;   	searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR location LIKE CONCAT('%', searchTerm, '%');

&#x20;

END //



DELIMITER ;



##### \-- ADD LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerLocation (

&#x20;   IN p\_location VARCHAR(255)

)



BEGIN

&#x09;DECLARE EXIT HANDLER FOR 1062

&#x20;

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot add: Location already exist.';

&#x20;   END;

&#x09;

&#x20;   INSERT INTO locker\_locations (location)

&#x20;   VALUES (p\_location);

END //



DELIMITER ;



##### \-- UPDATE LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLockerLocation (

&#x20;   IN p\_id INT,

&#x20;   IN p\_location VARCHAR(255)

)

BEGIN

&#x20;   UPDATE locker\_locations

&#x20;   SET

&#x20;       location = p\_location,

&#x20;       updated\_at = NOW()

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### \-- DELETE LOCKER LOCATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerLocation (

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DECLARE EXIT HANDLER FOR 1451

&#x20;

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot delete: Location still contains slots.';

&#x20;   END;

&#x20;

&#x20;   DELETE FROM locker\_locations

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### \-- GET LOCKER SIZES NORMAL (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerSizesNormal()



BEGIN

&#x20;   SELECT \* FROM locker\_sizes;

END //



DELIMITER ;



##### \-- GET LOCKER SIZES (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerSizes(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;   	id,

&#x20;       size,

&#x20;       price,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at,

&#x09;	DATE\_FORMAT(updated\_at, '%M %d, %Y %h:%i:%s %p') AS updated\_at

&#x20;   	FROM locker\_sizes

&#x20;   ORDER BY created\_at DESC LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS lockerSizesTotal

&#x20;   FROM locker\_sizes;

END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER LOCKER SIZES (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerSizes (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       size,

&#x20;       price,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at,

&#x09;	DATE\_FORMAT(updated\_at, '%M %d, %Y %h:%i:%s %p') AS updated\_at

&#x20;   FROM locker\_sizes



&#x20;   WHERE (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR price LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'a-z' THEN size END ASC,

&#x20;       CASE WHEN filterTerm = 'z-a' THEN size END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN filterTerm = 'newest' THEN created\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'cheaper' THEN price END ASC,

&#x20;       CASE WHEN filterTerm = 'expensive' THEN price END DESC

&#x20;

&#x09;LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS lockerSizesTotal

&#x20;   FROM locker\_sizes

&#x09;	WHERE (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR price LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- ADD LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerSize (

&#x20;   IN p\_size VARCHAR(255),

&#x20;   IN p\_price DOUBLE(10,2)

)



BEGIN

&#x09;DECLARE EXIT HANDLER FOR 1062

&#x20;

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot add: Size already exist.';

&#x20;   END;

&#x20;

&#x20;   INSERT INTO locker\_sizes (size, price)

&#x20;   VALUES (p\_size, p\_price);

END //



DELIMITER ;



##### \-- UPDATE LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLockerSize(

&#x20;   IN p\_id INT,

&#x20;   IN p\_size VARCHAR(255),

&#x20;   IN p\_price DOUBLE

)

BEGIN

&#x20;   UPDATE locker\_sizes

&#x20;   SET size = p\_size,

&#x20;       price = p\_price,

&#x09;updated\_at = NOW()

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### \-- DELETE LOCKER SIZE (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerSize(

&#x20;   IN p\_id INT

)



BEGIN

&#x09;DECLARE EXIT HANDLER FOR 1451

&#x20;

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot delete: Size still assigned to slots.';

&#x20;   END;

&#x20;

&#x20;   DELETE FROM locker\_sizes

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### \-- GET LOGS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLogs(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       action,

&#x20;       description,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM logs

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_offset, p\_limit;



&#x20;   SELECT COUNT(\*) AS logsTotal

&#x20;   FROM logs;



END //



DELIMITER ;



##### \-- GET ACADEMIC CALENDAR NORMAL (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAcademicCalendarNormal()



BEGIN

&#x20;   SELECT \* FROM academic\_calendar;

END //



DELIMITER ;



##### \-- GET ACADEMIC CALENDAR (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAcademicCalendar(

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;   	id,

&#x20;       academic\_year,

&#x20;       semester,

&#x20;       DATE\_FORMAT(start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   	FROM academic\_calendar

&#x20;   ORDER BY created\_at DESC LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS academicCalendarTotal

&#x20;   FROM academic\_calendar;

END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER ACADEMIC CALENDAR (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterAcademicCalendar (

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)

BEGIN

&#x20;   SELECT

&#x20;       id,

&#x20;       academic\_year,

&#x20;       semester,

&#x20;       DATE\_FORMAT(start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(created\_at, '%M %d, %Y %h:%i:%s %p') AS created\_at

&#x20;   FROM academic\_calendar



&#x20;   WHERE (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'a-z' THEN academic\_year END ASC,

&#x20;       CASE WHEN filterTerm = 'z-a' THEN academic\_year END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN created\_at END ASC,

&#x20;       CASE WHEN filterTerm = 'newest' THEN created\_at END DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;

&#x20;

&#x20;   SELECT COUNT(\*) AS academicCalendarTotal

&#x20;   FROM academic\_calendar

&#x20;   WHERE (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''

&#x20;       OR academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- GET LOCKERS BY LOCATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockersByLocation (

&#x20;   IN p\_location\_id INT

)



BEGIN

&#x20;   SELECT

&#x20;       ls.id,

&#x20;       ls.slot\_number,

&#x20;       ls.size\_id,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       ls.status,

&#x20;       ls.academic\_year\_id,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       la.user\_id

&#x20;   FROM locker\_slots ls



&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   LEFT JOIN locker\_applications la ON ls.id = la.slot\_id AND la.status = 'Accepted'



&#x20;   WHERE ls.location\_id = p\_location\_id

&#x20;   ORDER BY ls.slot\_number ASC;

&#x20;

END //



DELIMITER ;



##### \-- GET USER LOCKER APPLICATION ID (PROCEDURE)



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



##### \-- GET USER LOCKER APPLICATION DETAILS (PROCEDURE)



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



##### \-- ADD LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE addLocker (

&#x20;   IN p\_slot\_number INT,

&#x20;   IN p\_location\_id INT,

&#x20;   IN p\_size\_id INT,

&#x20;   IN p\_academic\_year\_id INT

)

BEGIN

&#x20;   DECLARE v\_start\_at DATE;

&#x20;   DECLARE v\_end\_at DATE;

&#x20;   DECLARE v\_status VARCHAR(50);



&#x20;   DECLARE EXIT HANDLER FOR 1062

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot add: Slot number already exists.';

&#x20;   END;



&#x20;   SELECT start\_at, end\_at INTO v\_start\_at, v\_end\_at

&#x20;   FROM academic\_calendar WHERE id = p\_academic\_year\_id;



&#x20;   IF CURDATE() < v\_start\_at THEN

&#x20;       SET v\_status = 'Not yet started';



&#x20;   ELSEIF CURDATE() > v\_end\_at THEN

&#x20;       SET v\_status = 'Already closed';



&#x20;   ELSE

&#x20;       SET v\_status = 'Available';

&#x20;   END IF;



&#x20;   INSERT INTO locker\_slots (

&#x20;       slot\_number,

&#x20;       location\_id,

&#x20;       size\_id,

&#x20;       academic\_year\_id,

&#x20;       status

&#x20;   )

&#x20;   VALUES (

&#x20;       p\_slot\_number,

&#x20;       p\_location\_id,

&#x20;       p\_size\_id,

&#x20;       p\_academic\_year\_id,

&#x20;       v\_status

&#x20;   );



END //



DELIMITER ;



##### \-- UPDATE LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLocker (

&#x20;   IN p\_id INT,

&#x20;   IN p\_slot\_number INT,

&#x20;   IN p\_size\_id INT,

&#x20;   IN p\_academic\_year\_id INT,

&#x20;   IN p\_status VARCHAR(255)

)



BEGIN

&#x20;   DECLARE v\_start\_at DATE;

&#x20;   DECLARE v\_end\_at DATE;

&#x20;   DECLARE v\_status VARCHAR(255);



&#x20;   DECLARE EXIT HANDLER FOR 1062

&#x20;   BEGIN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot update: Slot number already exists.';

&#x20;   END;



&#x20;   SELECT start\_at, end\_at INTO v\_start\_at, v\_end\_at

&#x20;   FROM academic\_calendar WHERE id = p\_academic\_year\_id;



&#x20;   IF p\_status = 'Occupied' THEN

&#x20;       SET v\_status = 'Occupied';



&#x20;   ELSE

&#x20;       IF CURDATE() < v\_start\_at THEN

&#x20;           SET v\_status = 'Not yet started';



&#x20;       ELSEIF CURDATE() > v\_end\_at THEN

&#x20;           SET v\_status = 'Already closed';



&#x20;       ELSE

&#x20;           SET v\_status = 'Available';

&#x20;       END IF;

&#x20;   END IF;



&#x20;   UPDATE locker\_slots

&#x20;   SET

&#x20;       slot\_number = p\_slot\_number,

&#x20;       size\_id = p\_size\_id,

&#x20;       academic\_year\_id = p\_academic\_year\_id,

&#x20;       status = v\_status,

&#x20;       updated\_at = NOW()

&#x20;   WHERE id = p\_id;



END //



DELIMITER ;



##### \-- DELETE LOCKER (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLocker (

&#x20;   IN p\_id INT

)



BEGIN

&#x20;   DECLARE v\_status VARCHAR(255);

&#x20;   DECLARE v\_block\_count INT DEFAULT 0;



&#x20;   SELECT status INTO v\_status

&#x20;   FROM locker\_slots

&#x20;   WHERE id = p\_id;



&#x20;   SELECT COUNT(\*) INTO v\_block\_count FROM locker\_applications

&#x20;   WHERE slot\_id = p\_id

&#x20;     AND (

&#x20;           status = 'Pending'

&#x20;           OR status = 'Accepted'

&#x20;           OR (status = 'Ended' AND payment = 'Unpaid')

&#x20;     );



&#x20;   IF v\_status = 'Occupied' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot delete: Locker is currently occupied.';



&#x20;   ELSEIF v\_block\_count > 0 THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Cannot delete: Locker has active applications.';



&#x20;   ELSE

&#x20;       DELETE FROM locker\_slots

&#x20;       WHERE id = p\_id;



&#x20;   END IF;



END //



DELIMITER ;



##### \-- ACCEPT LOCKER APPLICATION (PROCEDURE)



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



##### \-- REJECT LOCKER APPLICATION (PROCEDURE)



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



##### \-- REVOKE LOCKER APPLICATION (PROCEDURE)



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



##### \-- PAID LOCKER APPLICATION (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE paidLockerApplication(

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   DECLARE v\_slot\_id INT;

&#x20;   DECLARE v\_status VARCHAR(255);

&#x20;   DECLARE v\_payment VARCHAR(255);



&#x20;   START TRANSACTION;



&#x20;   SELECT slot\_id, status, payment

&#x20;   INTO v\_slot\_id, v\_status, v\_payment

&#x20;   FROM locker\_applications

&#x20;   WHERE id = p\_application\_id;



&#x20;   IF v\_status IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Application not found';



&#x20;   ELSEIF v\_status <> 'Ended' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Only ended applications can be paid';

&#x20;   END IF;



&#x20;   UPDATE locker\_applications

&#x20;   SET payment = 'Paid', updated\_at = NOW()

&#x20;   WHERE id = p\_application\_id;



&#x20;   COMMIT;



END //



DELIMITER ;



##### \-- GET MY PENDING LOCKER APPLICATIONS (PROCEDURE)



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

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x09;ac.academic\_year,

&#x09;ac.semester,

&#x09;DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id AND la.status = 'Pending'

&#x20;   ORDER BY la.created\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myPendingTotal FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id AND status = 'Pending';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER MY PENDING LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterMyPendingLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.created\_at, '%M %d, %Y') AS created\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Pending'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.created\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.created\_at END ASC,

&#x20;       la.created\_at DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myPendingTotal

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Pending'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- GET MY ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



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

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x09;ac.academic\_year,

&#x09;ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id AND la.status = 'Accepted'

&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myAcceptedTotal FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id AND status = 'Accepted';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER MY ACCEPTED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterMyAcceptedLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Accepted'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myAcceptedTotal

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Accepted'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- GET MY ENDED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyEndedLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x09;ac.academic\_year,

&#x09;ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id AND la.status = 'Ended' AND la.payment = 'Unpaid'



&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myEndedTotal FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id AND status = 'Ended' AND payment = 'Unpaid';



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER MY ENDED LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterMyEndedLockerApplications(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Ended' AND la.payment = 'Unpaid'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myEndedTotal

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status = 'Ended' AND la.payment = 'Unpaid'



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- GET MY LOCKER APPLICATION HISTORY (PROCEDURE)



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

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x09;ac.academic\_year,

&#x09;ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')

&#x20;   ORDER BY la.updated\_at DESC LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myHistoryTotal FROM locker\_applications

&#x20;   WHERE user\_id = p\_user\_id AND status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND payment IN ('Paid', 'Unpaid');



END //



DELIMITER ;



##### \-- GET SEARCH AND FILTER MY HISTORY LOCKER APPLICATIONS (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterMyLockerApplicationHistory(

&#x20;   IN p\_user\_id VARCHAR(255),

&#x20;   IN searchTerm VARCHAR(255),

&#x20;   IN filterTerm VARCHAR(255),

&#x20;   IN p\_limit INT,

&#x20;   IN p\_offset INT

)



BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       la.payment,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price,

&#x20;       ac.academic\_year,

&#x20;       ac.semester,

&#x20;       DATE\_FORMAT(ac.start\_at, '%M %d, %Y') AS start\_at,

&#x20;       DATE\_FORMAT(ac.end\_at, '%M %d, %Y') AS end\_at,

&#x20;       DATE\_FORMAT(la.updated\_at, '%M %d, %Y') AS updated\_at

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   )



&#x20;   ORDER BY

&#x20;       CASE WHEN filterTerm = 'newest' THEN la.updated\_at END DESC,

&#x20;       CASE WHEN filterTerm = 'oldest' THEN la.updated\_at END ASC,

&#x20;       la.updated\_at DESC



&#x20;   LIMIT p\_limit OFFSET p\_offset;



&#x20;   SELECT COUNT(\*) AS myHistoryTotal

&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   LEFT JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id



&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;   AND la.status IN ('Cancelled', 'Rejected', 'Revoked', 'Ended') AND la.payment IN ('Paid', 'Unpaid')



&#x20;   AND (

&#x20;       searchTerm IS NULL

&#x20;       OR searchTerm = ''



&#x20;       OR la.id LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ls.slot\_number LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ll.location LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR sz.size LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.academic\_year LIKE CONCAT('%', searchTerm, '%')

&#x20;       OR ac.semester LIKE CONCAT('%', searchTerm, '%')

&#x20;   );



END //



DELIMITER ;



##### \-- APPLY LOCKER (PROCEDURE)



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

&#x20;   DECLARE slot\_exists INT DEFAULT 0;



&#x20;   SELECT

&#x20;       ls.status,

&#x20;       ac.start\_at,

&#x20;       ac.end\_at

&#x20;   INTO

&#x20;       slot\_status,

&#x20;       v\_start\_at,

&#x20;       v\_end\_at

&#x20;   FROM locker\_slots ls

&#x20;   LEFT JOIN academic\_calendar ac

&#x20;       ON ls.academic\_year\_id = ac.id

&#x20;   WHERE ls.id = u\_slot\_id;



&#x20;   IF slot\_status IS NULL THEN

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



##### \-- CANCEL LOCKER APPLICATION (PROCEDURE)



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



##### \-- GET TOTAL LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getTotalLockersCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS total\_lockers\_count

&#x20;   FROM locker\_slots;

END //



DELIMITER ;



##### \-- GET AVAILABLE LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAvailableLockersCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS available\_lockers\_count

&#x20;   FROM locker\_slots

&#x20;   WHERE status = 'Available';

END //



DELIMITER ;



##### \-- GET OCCUPIED LOCKERS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getOccupiedLockersCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS occupied\_lockers\_count

&#x20;   FROM locker\_slots

&#x20;   WHERE status = 'Occupied';

END //



DELIMITER ;



##### \-- GET TOTAL LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getTotalLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS total\_locker\_applications\_count

&#x20;   FROM locker\_applications;

END //



DELIMITER ;



##### \-- GET PENDING LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getPendingLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS pending\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Pending';

END //



DELIMITER ;



##### \-- GET CANCELLED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getCancelledLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS cancelled\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Cancelled';

END //



DELIMITER ;



##### \-- GET ACCEPTED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getAcceptedLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS accepted\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Accepted';

END //



DELIMITER ;



##### \-- GET REJECTED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getRejectedLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS rejected\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Rejected';

END //



DELIMITER ;



##### \-- GET REVOKED LOCKER APPLICATIONS COUNT (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE getRevokedLockerApplicationsCount()

BEGIN

&#x20;   SELECT COUNT(\*) AS revoked\_locker\_applications\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE status = 'Revoked';

END //



DELIMITER ;



##### \-- AFTER LOCKER LOCATION INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_insertion

AFTER INSERT ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Add';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Location ', NEW.location, ' has been added.');



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER LOCKER SIZES INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_insertion

AFTER INSERT ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Add';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT('Size ', NEW.size, ' with price ₱', NEW.price, ' has been added.');



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER LOCKER SLOT INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_insertion

AFTER INSERT ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Add';

&#x20;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE v\_size VARCHAR(255);

&#x20;   DECLARE v\_start\_at DATE;

&#x20;   DECLARE v\_end\_at DATE;

&#x20;

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SELECT location INTO v\_location FROM locker\_locations

&#x20;   WHERE id = NEW.location\_id LIMIT 1;



&#x20;   SELECT size INTO v\_size FROM locker\_sizes

&#x20;   WHERE id = NEW.size\_id LIMIT 1;



&#x20;   SELECT start\_at, end\_at INTO v\_start\_at, v\_end\_at FROM academic\_calendar

&#x20;   WHERE id = NEW.academic\_year\_id LIMIT 1;



&#x20;   SET description = CONCAT(

&#x20;       'Slot #',

&#x20;       NEW.slot\_number,

&#x20;       ' (',

&#x20;       v\_location, ', ',

&#x20;       v\_size, ', ',

&#x20;       DATE\_FORMAT(v\_start\_at, '%M %d, %Y'),

&#x20;       ' - ',

&#x20;       DATE\_FORMAT(v\_end\_at, '%M %d, %Y'),

&#x20;       ', ',

&#x20;       NEW.status,

&#x20;       ') has been added.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER LOCKER LOCATION UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_updation

AFTER UPDATE ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Update';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Location ',

&#x20;       OLD.location,

&#x20;       ' has been updated to ',

&#x20;       NEW.location,

&#x20;       '.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER LOCKER SIZES UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_updation

AFTER UPDATE ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Update';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Size ',

&#x20;       OLD.size,

&#x20;       ' with price ₱',

&#x20;       OLD.price,

&#x20;       ' has been updated to ',

&#x20;       NEW.size,

&#x20;       ' with price ₱',

&#x20;       NEW.price,

&#x20;       '.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER LOCKER SLOT UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_updation

AFTER UPDATE ON locker\_slots

FOR EACH ROW

BEGIN



&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Update';



&#x20;   DECLARE v\_old\_location VARCHAR(255);

&#x20;   DECLARE v\_new\_location VARCHAR(255);

&#x20;   DECLARE v\_old\_size VARCHAR(255);

&#x20;   DECLARE v\_new\_size VARCHAR(255);

&#x20;   DECLARE v\_old\_start\_at DATE;

&#x20;   DECLARE v\_old\_end\_at DATE;

&#x20;   DECLARE v\_new\_start\_at DATE;

&#x20;   DECLARE v\_new\_end\_at DATE;



&#x20;   DECLARE description TEXT;



&#x20;   SELECT location INTO v\_old\_location FROM locker\_locations

&#x20;   WHERE id = OLD.location\_id LIMIT 1;



&#x20;   SELECT location INTO v\_new\_location FROM locker\_locations

&#x20;   WHERE id = NEW.location\_id LIMIT 1;



&#x20;   SELECT size INTO v\_old\_size FROM locker\_sizes

&#x20;   WHERE id = OLD.size\_id LIMIT 1;



&#x20;   SELECT size INTO v\_new\_size FROM locker\_sizes

&#x20;   WHERE id = NEW.size\_id LIMIT 1;



&#x20;   SELECT start\_at, end\_at INTO v\_old\_start\_at, v\_old\_end\_at FROM academic\_calendar

&#x20;   WHERE id = OLD.academic\_year\_id LIMIT 1;



&#x20;   SELECT start\_at, end\_at INTO v\_new\_start\_at, v\_new\_end\_at FROM academic\_calendar

&#x20;   WHERE id = NEW.academic\_year\_id LIMIT 1;



&#x20;   SET description = CONCAT(

&#x20;       'Slot #',

&#x20;       OLD.slot\_number,

&#x20;       ' (',

&#x20;       v\_old\_location,

&#x20;       ', ',

&#x20;       v\_old\_size,

&#x20;       ', ',

&#x20;       DATE\_FORMAT(v\_old\_start\_at, '%M %d, %Y'),

&#x20;       ' - ',

&#x20;       DATE\_FORMAT(v\_old\_end\_at, '%M %d, %Y'),

&#x20;       ', ',

&#x20;       OLD.status,

&#x20;       ') updated to Slot #',

&#x20;       NEW.slot\_number,

&#x20;       ' (', v\_new\_location, ', ', v\_new\_size,

&#x20;       ', ',

&#x20;       DATE\_FORMAT(v\_new\_start\_at, '%M %d, %Y'),

&#x20;       ' - ',

&#x20;       DATE\_FORMAT(v\_new\_end\_at, '%M %d, %Y'),

&#x20;       ', ',

&#x20;       NEW.status,

&#x20;       ').'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER LOCKER LOCATION DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_location\_deletion

AFTER DELETE ON locker\_locations

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Delete';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Location ',

&#x20;       OLD.location,

&#x20;       ' has been deleted.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER LOCKER SIZES DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_sizes\_deletion

AFTER DELETE ON locker\_sizes

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Delete';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Size ',

&#x20;       OLD.size,

&#x20;       ' (₱',

&#x20;       OLD.price,

&#x20;       ') has been deleted.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER LOCKER SLOT DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_deletion

AFTER DELETE ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Delete';

&#x20;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE v\_size VARCHAR(255);

&#x20;   DECLARE v\_start\_at DATE;

&#x20;   DECLARE v\_end\_at DATE;

&#x20;

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SELECT location INTO v\_location FROM locker\_locations

&#x20;   WHERE id = OLD.location\_id LIMIT 1;



&#x20;   SELECT size INTO v\_size FROM locker\_sizes

&#x20;   WHERE id = OLD.size\_id LIMIT 1;

&#x20;

&#x20;   SELECT start\_at, end\_at INTO v\_start\_at, v\_end\_at FROM academic\_calendar

&#x20;   WHERE id = OLD.academic\_year\_id LIMIT 1;



&#x20;   SET description = CONCAT(

&#x20;       'Slot #',

&#x20;       OLD.slot\_number,

&#x20;       ' (',

&#x20;       v\_location,

&#x20;       ', ',

&#x20;       v\_size,

&#x20;       ', ',

&#x20;       DATE\_FORMAT(v\_start\_at, '%M %d, %Y'),

&#x20;       ' - ',

&#x20;       DATE\_FORMAT(v\_end\_at, '%M %d, %Y'),

&#x20;       ', ',

&#x20;       OLD.status,

&#x20;       ') has been deleted.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR INSERTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_calendar\_insertion

AFTER INSERT ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Add';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Academic year ',

&#x20;       NEW.academic\_year,

&#x20;       ' (',

&#x20;       NEW.semester,

&#x20;       ') has been added from ',

&#x20;       DATE\_FORMAT(NEW.start\_at, '%M %d, %Y'),

&#x20;       ' to ',

&#x20;       DATE\_FORMAT(NEW.end\_at, '%M %d, %Y'),

&#x20;       '.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR UPDATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_calendar\_updation

AFTER UPDATE ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Update';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Academic year ',

&#x20;       OLD.academic\_year,

&#x20;       ' (',

&#x20;       OLD.semester,

&#x20;       ') has been updated to ',

&#x20;       NEW.academic\_year,

&#x20;       ' (',

&#x20;       NEW.semester,

&#x20;       ') from ',

&#x20;       DATE\_FORMAT(NEW.start\_at, '%M %d, %Y'),

&#x20;       ' to ',

&#x20;       DATE\_FORMAT(NEW.end\_at, '%M %d, %Y'),

&#x20;       '.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR DELETION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_calendar\_deletion

AFTER DELETE ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Delete';

&#x20;   DECLARE description VARCHAR(255);



&#x20;   SET description = CONCAT(

&#x20;       'Academic year ',

&#x20;       OLD.academic\_year,

&#x20;       ' (',

&#x20;       OLD.semester,

&#x20;       ') from ',

&#x20;       DATE\_FORMAT(OLD.start\_at, '%M %d, %Y'),

&#x20;       ' to ',

&#x20;       DATE\_FORMAT(OLD.end\_at, '%M %d, %Y'),

&#x20;       ' has been deleted.'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );

END //



DELIMITER ;



##### \-- AFTER LOCKER APPLICATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_application

AFTER INSERT ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Apply';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   WHERE ls.id = NEW.slot\_id LIMIT 1;



&#x20;   SET description = CONCAT(

&#x09;'User ',

&#x20;       NEW.user\_id,

&#x20;       ' applied (Slot #',

&#x20;       v\_slot\_number,

&#x20;       ', ',

&#x20;       v\_location,

&#x20;       ').'

&#x20;   );



&#x20;   INSERT INTO logs (

&#x20;       action,

&#x20;       description

&#x20;   ) VALUES (

&#x20;       action,

&#x20;       description

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER LOCKER CANCELLATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_cancellation

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Cancel';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   IF NEW.status = 'Cancelled' AND OLD.status <> 'Cancelled' THEN



&#x20;       SELECT ls.slot\_number, ll.location INTO v\_slot\_number, v\_location FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id LIMIT 1;



&#x20;       SET description = CONCAT(

&#x20;           'User ',

&#x20;           NEW.user\_id,

&#x20;           ' cancelled (Slot #',

&#x20;           v\_slot\_number,

&#x20;           ', ',

&#x20;           v\_location,

&#x20;           ').'

&#x20;       );



&#x20;       INSERT INTO logs (

&#x20;           action,

&#x20;           description

&#x20;       ) VALUES (

&#x20;           action,

&#x20;           description

&#x20;       );



&#x20;   END IF;



END //



DELIMITER ;



##### \-- AFTER LOCKER ACCEPTATION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_acception

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Accept';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   IF NEW.status = 'Accepted' AND OLD.status <> 'Accepted' THEN



&#x20;       SELECT ls.slot\_number, ll.location

&#x20;       INTO v\_slot\_number, v\_location

&#x20;       FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(

&#x09;    'User ',

&#x20;           NEW.user\_id,

&#x20;           ' application on slot #',

&#x20;           v\_slot\_number,

&#x20;           ' at ',

&#x20;           v\_location,

&#x20;           ' accepted.'

&#x20;       );



&#x20;       INSERT INTO logs (action, description)

&#x20;       VALUES (action, description);



&#x20;   END IF;



END //



DELIMITER ;



##### \-- AFTER LOCKER REJECTION (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_rejection

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Reject';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   IF NEW.status = 'Rejected' AND OLD.status <> 'Rejected' THEN



&#x20;       SELECT ls.slot\_number, ll.location

&#x20;       INTO v\_slot\_number, v\_location

&#x20;       FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(

&#x09;    'User ',

&#x20;           NEW.user\_id,

&#x20;           ' application on slot #',

&#x20;           v\_slot\_number,

&#x20;           ' at ',

&#x20;           v\_location,

&#x20;           ' rejected.'

&#x20;       );



&#x20;       INSERT INTO logs (action, description)

&#x20;       VALUES (action, description);



&#x20;   END IF;



END //



DELIMITER ;



##### \-- AFTER LOCKER REVOKING (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_revoking

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Revoke';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   IF NEW.status = 'Revoked' AND OLD.status <> 'Revoked' THEN



&#x20;       SELECT ls.slot\_number, ll.location

&#x20;       INTO v\_slot\_number, v\_location

&#x20;       FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(

&#x09;    'User ',

&#x20;           NEW.user\_id,

&#x20;           ' application on slot #',

&#x20;           v\_slot\_number,

&#x20;           ' at ',

&#x20;           v\_location,

&#x20;           ' revoked.'

&#x20;       );



&#x20;       INSERT INTO logs (action, description)

&#x20;       VALUES (action, description);



&#x20;   END IF;



END //



DELIMITER ;



##### \-- AFTER LOCKER ENDED (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_ending

AFTER UPDATE ON locker\_applications

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'End';

&#x20;   DECLARE v\_slot\_number INT;

&#x20;   DECLARE v\_location VARCHAR(255);

&#x20;   DECLARE description TEXT;



&#x20;   IF NEW.status = 'Ended' AND OLD.status <> 'Ended' THEN



&#x20;       SELECT ls.slot\_number, ll.location

&#x20;       INTO v\_slot\_number, v\_location

&#x20;       FROM locker\_slots ls

&#x20;       INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;       WHERE ls.id = NEW.slot\_id

&#x20;       LIMIT 1;



&#x20;       SET description = CONCAT(

&#x09;    'User ',

&#x20;           NEW.user\_id,

&#x20;           ' application on slot #',

&#x20;           v\_slot\_number,

&#x20;           ' at ',

&#x20;           v\_location,

&#x20;           ' ended.'

&#x20;       );



&#x20;       INSERT INTO logs (action, description)

&#x20;       VALUES (action, description);



&#x20;   END IF;



END //



DELIMITER ;



##### \-- UPDATE LOCKER APPLICATION DAILY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE endLockerApplication\_applicationStatus()



BEGIN

&#x20;   UPDATE locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   SET

&#x20;       la.status = 'Ended',

&#x20;       la.payment = 'Unpaid',

&#x20;       la.updated\_at = NOW()

&#x20;   WHERE la.status = 'Accepted' AND CURRENT\_DATE() >= ac.end\_at;



&#x20;   UPDATE locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   SET

&#x20;       la.status = 'Cancelled',

&#x20;       la.updated\_at = NOW()

&#x20;   WHERE la.status = 'Pending'

&#x20;   AND CURRENT\_DATE() >= ac.end\_at;



END //



DELIMITER ;



##### \-- UPDATE LOCKER APPLICATION DAILY (EVENT)



DELIMITER //



CREATE OR REPLACE EVENT endLockerApplication\_applicationStatus

ON SCHEDULE EVERY 1 DAY

STARTS CURRENT\_TIMESTAMP

DO



BEGIN

&#x20;   CALL endLockerApplication\_applicationStatus();

END //



DELIMITER ;



##### \-- UPDATE LOCKER STATUS DAILY (PROCEDURE)



DELIMITER //



CREATE OR REPLACE PROCEDURE endLockerApplication\_lockerStatus()



BEGIN

&#x20;   UPDATE locker\_slots ls

&#x20;   INNER JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   SET ls.status = 'Not yet started'

&#x20;   WHERE CURRENT\_DATE() < ac.start\_at AND ls.status <> 'Occupied';



&#x20;   UPDATE locker\_slots ls

&#x20;   INNER JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   SET ls.status = 'Available'

&#x20;   WHERE CURRENT\_DATE() >= ac.start\_at AND CURRENT\_DATE() <= ac.end\_at AND ls.status <> 'Occupied';



&#x20;   UPDATE locker\_slots ls

&#x20;   INNER JOIN academic\_calendar ac ON ls.academic\_year\_id = ac.id

&#x20;   SET ls.status = 'Already closed'

&#x20;   WHERE CURRENT\_DATE() >= ac.end\_at;



END //



DELIMITER ;



##### \-- UPDATE LOCKER STATUS DAILY (EVENT)



DELIMITER //



CREATE OR REPLACE EVENT endLockerApplication\_lockerStatus

ON SCHEDULE EVERY 1 DAY

STARTS CURRENT\_TIMESTAMP

DO



BEGIN

&#x20;   CALL endLockerApplication\_lockerStatus();

END //



DELIMITER ;

