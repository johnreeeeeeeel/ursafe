# (NEW) URSAFE

# 

# URSAFE PRODECURE



##### GET USER BY ID



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserById(IN u\_id VARCHAR(255))

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

&#x20;   WHERE id = u\_id

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### GET ADMIN BY EMAIL



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



##### GET USER BY EMAIL



DELIMITER //



CREATE OR REPLACE PROCEDURE getUserByEmail(IN u\_email VARCHAR(255))

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

&#x20;   WHERE email = u\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### UPDATE USER PASSWORD



DELIMITER //



CREATE PROCEDURE updateUserPassword(

&#x20;   IN u\_email VARCHAR(255),

&#x20;   IN u\_password VARCHAR(255)

)

BEGIN

&#x20;   UPDATE users

&#x20;   SET password = u\_password

&#x20;   WHERE email = u\_email

&#x20;   LIMIT 1;

END //



DELIMITER ;



##### SEARCH AND FILTER USER



DELIMITER //



CREATE OR REPLACE PROCEDURE searchFilterUsers(

&#x20;   IN u\_search VARCHAR(255),

&#x20;   IN u\_filter VARCHAR(10)

)

BEGIN

&#x20;   SELECT \*

&#x20;   FROM view\_users

&#x20;   WHERE

&#x20;       (u\_search IS NULL OR u\_search = '' OR

&#x20;           firstname LIKE CONCAT('%', u\_search, '%')

&#x20;           OR middlename LIKE CONCAT('%', u\_search, '%')

&#x20;           OR lastname LIKE CONCAT('%', u\_search, '%')

&#x20;       )



&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN u\_filter = 'old' THEN id

&#x20;           ELSE NULL

&#x20;       END ASC,



&#x20;       CASE

&#x20;           WHEN u\_filter = 'new' THEN id

&#x20;           ELSE NULL

&#x20;       END DESC;

END //



DELIMITER ;



##### GET LOCKERS



DELIMITER //



CREATE PROCEDURE getLockers()

BEGIN

&#x20;   SELECT \* FROM locker\_slots\_view;

END //



DELIMITER ;



##### GET LOCKERS BY LOCATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockersByLocation (

&#x20;   IN p\_location\_id INT

)

BEGIN

&#x20;   SELECT 

&#x20;       ls.id,

&#x20;       ls.slot\_number,

&#x20;       lsz.size,

&#x20;       lsz.price,

&#x20;       ls.status

&#x20;   FROM locker\_slots ls

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE ls.location\_id = p\_location\_id

&#x20;   ORDER BY ls.slot\_number ASC;

END //



DELIMITER ;



##### GET LOCKER LOCATIONS



DELIMITER //



CREATE PROCEDURE getLockerLocations()

BEGIN

&#x20;   SELECT id, location FROM locker\_locations;

END //



DELIMITER ;



##### GET LOCKER LOCATIONS ASC



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLocationsASC()

BEGIN

&#x20;   SELECT id, location

&#x20;   FROM locker\_locations

&#x20;   ORDER BY location ASC;

END //



DELIMITER ;



##### GET LOCKER LOCATIONS DESC



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLocationsDESC()

BEGIN

&#x20;   SELECT id, location

&#x20;   FROM locker\_locations

&#x20;   ORDER BY location DESC;

END //



DELIMITER ;



##### GET SEARCH LOCKER LOCATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE searchLockerLocation (IN searchTerm VARCHAR(100))

BEGIN

&#x20;   SELECT id, location

&#x20;   FROM locker\_locations

&#x20;   WHERE location LIKE CONCAT('%', searchTerm, '%')

&#x20;   ORDER BY location ASC;

END //



DELIMITER ;



##### GET LOCKER SIZES



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerSizes()

BEGIN

&#x20;   SELECT id, size, price

&#x20;   FROM locker\_sizes

&#x20;   ORDER BY id ASC;

END //



DELIMITER ;



##### ADD LOCKER LOCATION



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerLocation (

&#x20;   IN p\_location VARCHAR(255)

)

BEGIN

&#x20;   INSERT INTO locker\_locations (location)

&#x20;   VALUES (p\_location);

END //



DELIMITER ;



##### UPDATE LOCKER LOCATION



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



##### DELETE LOCKER LOCATION



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerLocation (

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_locations

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### ADD LOCKER SIZE



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



##### UPDATE LOCKER SIZE



DELIMITER //



CREATE PROCEDURE updateLockerSize(

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



##### DELETE LOCKER SIZE



DELIMITER //



CREATE PROCEDURE deleteLockerSize(

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_sizes

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### ADD LOCKER SLOT



DELIMITER //



CREATE OR REPLACE PROCEDURE addLockerSlot (

&#x20;   IN p\_slot\_number INT,

&#x20;   IN p\_location\_id INT,

&#x20;   IN p\_size\_id INT

)

BEGIN

&#x20;   INSERT INTO locker\_slots (

&#x20;       slot\_number,

&#x20;       location\_id,

&#x20;       size\_id,

&#x20;       status

&#x20;   )

&#x20;   VALUES (

&#x20;       p\_slot\_number,

&#x20;       p\_location\_id,

&#x20;       p\_size\_id,

&#x20;       'Available'

&#x20;   );

END //



DELIMITER ;



##### UPDATE LOCKER SLOT



DELIMITER //



CREATE OR REPLACE PROCEDURE updateLockerSlot (

&#x20;   IN p\_id INT,

&#x20;   IN p\_slot\_number INT,

&#x20;   IN p\_status VARCHAR(255)

)

BEGIN

&#x20;   UPDATE locker\_slots

&#x20;   SET 

&#x20;       slot\_number = p\_slot\_number,

&#x20;       status = p\_status

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;



##### DELETE LOCKER SLOT



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerSlot (

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_slots

&#x20;   WHERE id = p\_id;

END //



DELIMITER ;





# URSAFE VIEW



##### VIEW USERS



CREATE OR REPLACE VIEW ursafe\_db.view\_users AS

SELECT

&#x20;   u.id,



&#x20;   TRIM(CONCAT(

&#x20;       u.firstname, ' ',

&#x20;       IFNULL(CONCAT(u.middlename, ' '), ''),

&#x20;       u.lastname

&#x20;   )) AS fullname,



&#x20;   u.firstname,

&#x20;   u.lastname,

&#x20;   u.middlename,

&#x20;   u.sex,

&#x20;   u.dob,



&#x20;   u.institute AS institute\_id,

&#x20;   i.description AS institute,



&#x20;   u.program AS program\_id,

&#x20;   p.description AS program,



&#x20;   u.username,

&#x20;   u.email



FROM ursafe\_db.users u

LEFT JOIN campus\_db.institutes i ON u.institute = i.id

LEFT JOIN campus\_db.programs p ON u.program = p.id;



##### VIEW LOCKER SLOTS



CREATE OR REPLACE VIEW locker\_slots\_view AS

SELECT 

&#x20;   ls.id,

&#x20;   ls.slot\_number,

&#x20;   ll.location,

&#x20;   lsz.size,

&#x20;   lsz.price,

&#x20;   ls.status

FROM locker\_slots ls

JOIN locker\_locations ll ON ls.location\_id = ll.id

JOIN locker\_sizes lsz ON ls.size\_id = lsz.id;



# CAMPUS DB TRIGGERS



##### SYNC STUDENTS UPDATE



DELIMITER //



CREATE OR REPLACE TRIGGER sync\_students\_update

AFTER UPDATE ON campus\_db.students

FOR EACH ROW

BEGIN

&#x20;   UPDATE ursafe\_db.users

&#x20;   SET

&#x20;       lastname = NEW.lastname,

&#x20;       firstname = NEW.firstname,

&#x20;       middlename = NEW.middlename,

&#x20;       sex = NEW.sex,

&#x20;       dob = NEW.dob,

&#x20;       institute = NEW.institute,

&#x20;       program = NEW.program,

&#x20;       email = NEW.email

&#x20;   WHERE id = NEW.student\_id;

END//



DELIMITER ;

