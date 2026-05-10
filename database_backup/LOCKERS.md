# LOCKERS



##### GET LOCKER APPLICATIONS HISTORY



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerApplicationHistory()

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

&#x20;       lsz.price



&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status IN ('Cancelled', 'Rejected', 'Revoked')



&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;



##### GET ACCEPTED LOCKER APPLICATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getAcceptedLockerApplications()

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

&#x20;       lsz.price



&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status IN ('Accepted')



&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;



##### GET PENDING LOCKER APPLICATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getPendingLockerApplications()

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

&#x20;       lsz.price



&#x20;   FROM locker\_applications la



&#x20;   INNER JOIN users u ON la.user\_id = u.id

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes lsz ON ls.size\_id = lsz.id

&#x20;   WHERE la.status IN ('Pending')



&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;

















##### GET LOCKER LOCATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getLockerLocations()

BEGIN

&#x20;   SELECT id, location FROM locker\_locations;

END //



DELIMITER ;



##### GET SEARCH AND FILTER LOCKER LOCATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerLocation (

&#x20;   IN p\_search VARCHAR(255),

&#x20;   IN p\_filter VARCHAR(10)

)

BEGIN

&#x20;   SELECT id, location

&#x20;   FROM locker\_locations

&#x20;   WHERE (p\_search IS NULL OR p\_search = ''

&#x20;          OR location LIKE CONCAT('%', p\_search, '%'))

&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN p\_filter = 'desc' THEN location

&#x20;       END DESC,

&#x20;       CASE

&#x20;           WHEN p\_filter = 'asc' OR p\_filter IS NULL OR p\_filter = '' THEN location

&#x20;       END ASC;

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



##### GET LOCKER LOCATION LOGS



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

&#x20;       created\_at

&#x20;   FROM locker\_logs

&#x20;   ORDER BY created\_at DESC

&#x20;   LIMIT p\_offset, p\_limit;

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



##### GET SEARCH AND FILTER LOCKER SIZES



DELIMITER //



CREATE OR REPLACE PROCEDURE getSearchFilterLockerSizes (

&#x20;   IN p\_search VARCHAR(255),

&#x20;   IN p\_filter VARCHAR(10)

)

BEGIN

&#x20;   SELECT id, size, price

&#x20;   FROM locker\_sizes

&#x20;   WHERE (p\_search IS NULL OR p\_search = ''

&#x20;          OR size LIKE CONCAT('%', p\_search, '%'))

&#x20;   ORDER BY

&#x20;       CASE

&#x20;           WHEN p\_filter = 'desc' THEN size

&#x20;       END DESC,

&#x20;       CASE

&#x20;           WHEN p\_filter = 'asc' OR p\_filter IS NULL OR p\_filter = '' THEN size

&#x20;       END ASC;

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



##### DELETE LOCKER SIZE



DELIMITER //



CREATE OR REPLACE PROCEDURE deleteLockerSize(

&#x20;   IN p\_id INT

)

BEGIN

&#x20;   DELETE FROM locker\_sizes

&#x20;   WHERE id = p\_id;

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



##### GET USER LOCKER APPLICATION ID



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



##### GET USER LOCKER APPLICATION DETAILS



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



##### ACCEPT LOCKER APPLICATION



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

&#x20;   SET status = 'Accepted'

&#x20;   WHERE id = p\_application\_id;



&#x20;   UPDATE locker\_slots

&#x20;   SET status = 'Occupied'

&#x20;   WHERE id = v\_slot\_id;



&#x20;   COMMIT;

END //



DELIMITER ;



##### REJECT LOCKER APPLICATION



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

&#x20;   SET status = 'Rejected'

&#x20;   WHERE id = p\_application\_id;



END //



DELIMITER ;



##### REVOKE LOCKER APPLICATION



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

&#x20;   SET status = 'Revoked'

&#x20;   WHERE id = p\_application\_id;



&#x20;   UPDATE locker\_slots

&#x20;   SET status = 'Available'

&#x20;   WHERE id = v\_slot\_id;



&#x20;   COMMIT;



END //



DELIMITER ;



##### GET MY PENDING LOCKER APPLICATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyPendingLockerApplications(IN p\_user\_id VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status IN ('Pending')

&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;



##### GET MY ACCEPTED LOCKER APPLICATIONS



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyAcceptedLockerApplications(IN p\_user\_id VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status IN ('Accepted')

&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;



##### GET MY LOCKER APPLICATION HISTORY



DELIMITER //



CREATE OR REPLACE PROCEDURE getMyLockerApplicationHistory(IN p\_user\_id VARCHAR(255))

BEGIN

&#x20;   SELECT

&#x20;       la.id AS application\_id,

&#x20;       la.status,

&#x20;       ls.slot\_number,

&#x20;       ll.location,

&#x20;       sz.size,

&#x20;       sz.price

&#x20;   FROM locker\_applications la

&#x20;   INNER JOIN locker\_slots ls ON la.slot\_id = ls.id

&#x20;   INNER JOIN locker\_locations ll ON ls.location\_id = ll.id

&#x20;   INNER JOIN locker\_sizes sz ON ls.size\_id = sz.id

&#x20;   WHERE la.user\_id = p\_user\_id

&#x20;     AND la.status IN ('Cancelled', 'Rejected', 'Revoked')

&#x20;   ORDER BY la.id DESC;

END //



DELIMITER ;



##### APPLY LOCKER



DELIMITER //



CREATE OR REPLACE PROCEDURE applyLocker (

&#x20;   IN u\_user\_id VARCHAR(255),

&#x20;   IN u\_slot\_id INT

)

BEGIN

&#x20;   DECLARE active\_application\_count INT DEFAULT 0;

&#x20;   DECLARE slot\_status VARCHAR(50);



&#x20;   SELECT status

&#x20;   INTO slot\_status

&#x20;   FROM locker\_slots

&#x20;   WHERE id = u\_slot\_id;



&#x20;   IF slot\_status IS NULL THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'Slot not found';



&#x20;   ELSEIF slot\_status <> 'Available' THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'This slot is not available for application';

&#x20;   END IF;



&#x20;   SELECT COUNT(\*)

&#x20;   INTO active\_application\_count

&#x20;   FROM locker\_applications

&#x20;   WHERE user\_id = u\_user\_id

&#x20;     AND slot\_id = u\_slot\_id

&#x20;     AND status IN ('Pending', 'Accepted');



&#x20;   IF active\_application\_count > 0 THEN

&#x20;       SIGNAL SQLSTATE '45000'

&#x20;       SET MESSAGE\_TEXT = 'You already have an active application for this slot';

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



##### CANCEL LOCKER APPLICATION



DELIMITER //



CREATE OR REPLACE PROCEDURE cancelLockerApplication (

&#x20;   IN p\_application\_id INT

)

BEGIN

&#x20;   UPDATE locker\_applications

&#x20;   SET status = 'Cancelled'

&#x20;   WHERE id = p\_application\_id

&#x20;     AND status = 'Pending';

END //



DELIMITER ;



# LOCKERS (TRIGGERS)



##### AFTER LOCKER LOCATION INSERTION



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



##### AFTER LOCKER SIZES INSERTION



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



##### AFTER LOCKER SLOT INSERTION



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



##### AFTER LOCKER LOCATION UPDATION



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



##### AFTER LOCKER SIZES UPDATION



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



##### AFTER LOCKER SLOT UPDATION



DELIMITER //



CREATE OR REPLACE TRIGGER after\_locker\_slot\_updation

AFTER UPDATE ON locker\_slots

FOR EACH ROW



BEGIN

&#x20;   DECLARE action VARCHAR(255) DEFAULT 'Updation';

&#x20;   DECLARE description VARCHAR(255);



&#x09;IF OLD.slot\_number <> NEW.slot\_number THEN

&#x09;	SET description = CONCAT('Slot #', OLD.slot\_number, ' has been updated to ', 'Slot #', NEW.slot\_number, '.');

&#x09;ELSEIF OLD.status <> NEW.status THEN

&#x09;	SET description = CONCAT('Slot #', NEW.slot\_number, ' status of ', OLD.status, ' has been updated to ', NEW.status, '.');

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



##### AFTER LOCKER LOCATION DELETION



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



##### AFTER LOCKER SIZES DELETION



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



##### AFTER LOCKER SLOT DELETION



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



##### AFTER LOCKER APPLICATION



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



##### AFTER LOCKER CANCELLATION



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



##### AFTER LOCKER ACCEPTATION



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



##### AFTER LOCKER REJECTION



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



##### AFTER LOCKER REVOKING



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

