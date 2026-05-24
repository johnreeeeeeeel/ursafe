# \-- CAMPUS DB



##### \-- AFTER STUDENT INSTERT (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_student\_insert

AFTER INSERT ON students

FOR EACH ROW



BEGIN

&#x20;   IF NOT EXISTS (

&#x20;       SELECT 1 FROM ursafe\_db.users WHERE id = NEW.id

&#x20;   ) THEN



&#x20;       INSERT INTO ursafe\_db.users (

&#x20;           id, 

&#x20;           lastname, 

&#x20;           firstname, 

&#x20;           middlename,

&#x20;           sex, 

&#x20;           dob, 

&#x20;           institute, 

&#x20;           program, 

&#x20;           email

&#x20;       )

&#x20;       VALUES (

&#x20;           NEW.id, 

&#x20;           NEW.lastname, 

&#x20;           NEW.firstname, 

&#x20;           NEW.middlename,

&#x20;           NEW.sex, 

&#x20;           NEW.dob, 

&#x20;           NEW.institute, 

&#x20;           NEW.program, 

&#x20;           NEW.email

&#x20;       );



&#x20;   END IF;



&#x20;   INSERT INTO ursafe\_db.user\_account\_logs (

&#x20;       action,

&#x20;       description

&#x20;   )

&#x20;   VALUES (

&#x20;       'Add',

&#x20;       CONCAT(

&#x20;           'Added user: ',

&#x20;           NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''),



&#x20;           ' | ', NEW.id,

&#x20;           ' | ', NEW.sex,

&#x20;           ' | ', NEW.dob,

&#x20;           ' | ', NEW.institute,

&#x20;           ' | ', NEW.program,

&#x20;           ' | ', NEW.email

&#x20;       )

&#x20;   );



END //



DELIMITER ;

&#x09;

##### \-- AFTER STUDENT UPDATE (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_student\_update

AFTER UPDATE ON students

FOR EACH ROW



BEGIN

&#x20;   IF EXISTS (

&#x20;       SELECT 1 FROM ursafe\_db.users WHERE id = NEW.id

&#x20;   ) THEN



&#x20;       UPDATE ursafe\_db.users

&#x20;       SET

&#x20;           lastname = NEW.lastname,

&#x20;           firstname = NEW.firstname,

&#x20;           middlename = NEW.middlename,

&#x20;           sex = NEW.sex,

&#x20;           dob = NEW.dob,

&#x20;           institute = NEW.institute,

&#x20;           program = NEW.program,

&#x20;           email = NEW.email

&#x20;       WHERE id = NEW.id;



&#x20;   END IF;



&#x20;   INSERT INTO ursafe\_db.user\_account\_logs (

&#x20;       action,

&#x20;       description

&#x20;   )

&#x20;   VALUES (

&#x20;       'Update',

&#x20;       CONCAT(

&#x20;           'Updated user: ',

&#x20;           NEW.lastname, ', ', NEW.firstname, ' ', IFNULL(NEW.middlename, ''),



&#x20;           ' | ', NEW.id,

&#x20;           ' | ', NEW.sex,

&#x20;           ' | ', NEW.dob,

&#x20;           ' | ', NEW.institute,

&#x20;           ' | ', NEW.program,

&#x20;           ' | ', NEW.email

&#x20;       )

&#x20;   );



END //



DELIMITER ;



##### \-- AFTER STUDENT DELETE (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_student\_delete

AFTER DELETE ON students

FOR EACH ROW



BEGIN

&#x20;   IF EXISTS (

&#x20;       SELECT 1 FROM ursafe\_db.users WHERE id = OLD.id

&#x20;   ) THEN



&#x20;       DELETE FROM ursafe\_db.users

&#x20;       WHERE id = OLD.id;



&#x20;   END IF;



&#x20;   INSERT INTO ursafe\_db.user\_account\_logs (

&#x20;       action,

&#x20;       description

&#x20;   )

&#x20;   VALUES (

&#x20;       'Delete',

&#x20;       CONCAT(

&#x20;           'Deleted user: ',

&#x20;           OLD.lastname, ', ', OLD.firstname, ' ', IFNULL(OLD.middlename, ''),



&#x20;           ' | ', OLD.id,

&#x20;           ' | ', OLD.sex,

&#x20;           ' | ', OLD.dob,

&#x20;           ' | ', OLD.institute,

&#x20;           ' | ', OLD.program,

&#x20;           ' | ', OLD.email

&#x20;       )

&#x20;   );



END //



DELIMITER ;

