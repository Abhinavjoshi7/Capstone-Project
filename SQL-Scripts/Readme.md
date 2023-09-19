
### SQL Script Readme

* This SQL script creates a database schema for a simple appointment booking system. It contains several table creation statements, alter table statements and a trigger statement. It also includes a CREATE USER statement to create a developer role for the database.

#### Table Creation Statements

* The authentication table stores email addresses and hashed passwords for user authentication.
* The Customer table stores customer details such as first name, last name, and phone number.
* The Waitlist table stores customer waitlist entries with the date and time requested, the number of spots requested, and a confirmation flag.
* The Appointment table stores customer appointment bookings with the date and time booked, the number of spots booked, and a confirmation flag. It also includes a foreign key reference to the availability table.
* The availability table stores date and time availability with a flag to indicate whether it is booked or not.

#### Alter Table Statements 
* Add a reset_token column to the authentication table for password resets.
* Add an Availability_ID column to the Appointment table as a foreign key reference to the availability table.
* Drop the unique key index on Date_Available in the availability table.
* Modify the Booked_YN column default value in the availability table to false.

#### Trigger Statements For Business Rules 
* Create a trigger to check if the current date time is not less than the current time for availability. If the combined datetime value of Date_Available and Time_Available is less than the current datetime, the trigger raises an error. 
  
#### Create your account in Database 
* Create an developer account called sb_developer and grant the appropriate privilages as stated in the strawberry.fields.sql. Make sure you are in the right database 

### Connecting to database 
* Refer to database-connection.php



