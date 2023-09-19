CREATE TABLE authentication (
  Email_ID VARCHAR(255) COLLATE utf8mb4_General_ci NOT NULL,
  Password CHAR(60) NOT NULL,
  PRIMARY KEY (Email_ID)
);
CREATE TABLE Customer (
  Email_ID VARCHAR(255) COLLATE utf8mb4_General_ci NOT NULL,
  FirstName VARCHAR(50) NOT NULL,
  LastName VARCHAR(50) NOT NULL,
  Phone VARCHAR(15) NOT NULL,
  PRIMARY KEY (Email_ID),
  FOREIGN KEY (Email_ID) REFERENCES authentication(Email_ID)
);
CREATE TABLE Waitlist (
  Waitlist_ID INT NOT NULL AUTO_INCREMENT,
  Email_ID VARCHAR(255) COLLATE utf8mb4_General_ci NOT NULL,
  Date_Available DATE NOT NULL DEFAULT CURRENT_DATE,
  Time_Available TIME NOT NULL DEFAULT CURRENT_TIME,
  Quantity INT NOT NULL,
  ConfirmYN CHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (Waitlist_ID),
  INDEX Waitlist_WaitlistID (Waitlist_ID),
  FOREIGN KEY (Email_ID) REFERENCES Customer(Email_ID)
);
--Please drop the appointment table and re-create it with the updated defination for unique foreign keys, and one to one relationship between appointment and availability table. 
CREATE TABLE Appointment (
  Appointment_ID INT NOT NULL AUTO_INCREMENT,
  Email_ID VARCHAR(255) COLLATE utf8mb4_General_ci NOT NULL,
  Availability_ID INT NOT NULL,
  Date_Booked DATE NOT NULL DEFAULT CURRENT_DATE,
  Time_Booked TIME NOT NULL DEFAULT CURRENT_TIME,
  Quantity INT NOT NULL,
  ConfirmYN CHAR(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (Appointment_ID),
  INDEX Appointment_AppointmentID (Appointment_ID),
  FOREIGN KEY (Email_ID) REFERENCES Customer(Email_ID),
  FOREIGN KEY (Availability_ID) REFERENCES availability(Availability_ID),
  UNIQUE KEY (Availability_ID)
);



--Create availability table 

CREATE TABLE availability (
  Availability_ID INT NOT NULL AUTO_INCREMENT,
  Date_Available DATE NOT NULL,
  Time_Available TIME NOT NULL,
  Booked_YN  BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (Availability_ID),
  UNIQUE KEY (Date_Available, Time_Available)
); 
--Unique key is used here to ensure that data is not duplicated within a table, it has been removed through the alter table as it is no longer a business requirement 



--Alter Table Statements 
--Add the auth_token column for password resets
ALTER TABLE authentication
ADD reset_token VARCHAR(255);

--For blocking customers 
ALTER TABLE customer ADD Blocked Varchar(1) Default 'N'; 

--Add Availability_ID as a foreign key 
ALTER TABLE Appointment
ADD COLUMN Availability_ID INT NOT NULL,
ADD CONSTRAINT FK_Appointment_availability
FOREIGN KEY (Availability_ID)
REFERENCES availability (Availability_ID);

 --Drop the unique key index in availability 
 --Make sure to double check the index name in the availability table before dropping it 
 Alter table availability drop index Date_Available
--Alter the availability table to set the BookedYN default to false 
Alter Table availability MODIFY Booked_YN Boolean Not Null Default False ; 

--Alter the waitlist table to store an extra column for time. If the customer leaves it blank, it will set to full day  
ALTER TABLE waitlist
ADD COLUMN Time_Available_Until TIME NOT NULL DEFAULT CONCAT(HOUR('23:59:59'), ':', MINUTE('23:59:59'), ':', SECOND('23:59:59'));

--Alter the appointment table for storing comments for additional information
Alter Table appointment Add Column Comments text Default Null 

--Create a trigger that check if the current date time is not less than the current time for availabilty 
DELIMITER $$
CREATE TRIGGER `tr_availability_datetime_check` BEFORE INSERT ON `availability`
FOR EACH ROW
BEGIN
  IF CONCAT(NEW.Date_Available, ' ', NEW.Time_Available) < NOW() THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The combined datetime value of Date_Available and Time_Available cannot be less than the current datetime';
  END IF;
END $$
DELIMITER ;
--Note: The TIME data type in MySQL and MariaDB has a range of '00:00:00' to '23:59:59', which means you cannot use '24:00:00' as a valid time. The last event in that case would be 23:59:59
--Note: The DELIMITER statements are used to change the delimiter from the default semicolon to the double dollar sign ($$) before defining the trigger, and then back to the semicolon after defining the trigger. This is necessary because the trigger definition contains semicolons, which would normally be interpreted as the end of the SQL statement. Changing the delimiter allows the trigger definition to be treated as a single block of code
DELIMITER $$
CREATE TRIGGER availability_limit
BEFORE INSERT ON availability
FOR EACH ROW
BEGIN
    DECLARE count INT;
    SELECT COUNT(*) INTO count
    FROM availability
    WHERE Date_Available = NEW.Date_Available
    AND Time_Available = NEW.Time_Available
    AND Booked_YN = FALSE;
    IF count > 4 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'You cannot have more than 4 available slots for the same date and time combination';
    END IF;
END$$
DELIMITER ;





--Create a developer role in database 
--db password sbfieldsab140
CREATE USER 'sb_developer'@'localhost' IDENTIFIED VIA mysql_native_password USING '***';GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON *.* TO 'sb_developer'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;