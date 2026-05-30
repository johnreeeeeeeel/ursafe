# \-- SCHOOL DB



##### \-- AFTER STUDENT INSERT (TRIGGER)



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

END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR INSERT (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_year\_insert

AFTER INSERT ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   IF NOT EXISTS (

&#x20;       SELECT 1 FROM ursafe\_db.academic\_calendar WHERE id = NEW.id

&#x20;   ) THEN



&#x20;       INSERT INTO ursafe\_db.academic\_calendar (

&#x20;           id,

&#x20;           academic\_year,

&#x20;           semester,

&#x20;           start\_at,

&#x20;           end\_at,

&#x20;           created\_at

&#x20;       )

&#x20;       VALUES (

&#x20;           NEW.id,

&#x20;           NEW.academic\_year,

&#x20;           NEW.semester,

&#x20;           NEW.start\_at,

&#x20;           NEW.end\_at,

&#x20;           NEW.created\_at

&#x20;       );

&#x20;   END IF;

END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR UPDATE (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_year\_update

AFTER UPDATE ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   UPDATE ursafe\_db.academic\_calendar

&#x20;   SET

&#x20;       academic\_year = NEW.academic\_year,

&#x20;       semester = NEW.semester,

&#x20;       start\_at = NEW.start\_at,

&#x20;       end\_at = NEW.end\_at,

&#x20;       created\_at = NEW.created\_at

&#x20;   WHERE id = NEW.id;



END //



DELIMITER ;



##### \-- AFTER ACADEMIC YEAR DELETE (TRIGGER)



DELIMITER //



CREATE OR REPLACE TRIGGER after\_academic\_year\_delete

AFTER DELETE ON academic\_calendar

FOR EACH ROW



BEGIN

&#x20;   DELETE FROM ursafe\_db.academic\_calendar

&#x20;   WHERE id = OLD.id;



END //



DELIMITER ;

