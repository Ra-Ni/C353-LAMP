# COMP-353 LAMP Project

## Requirements

- Web Browser (Firefox, Microsoft Edge, Safari, ...)
- A csv file similar to "db19s-P1.csv"

## Usage

1. Connect to [QR_353_2's ENCS Server](https://qrc353.encs.concordia.ca/upload.php) using a web browser

2. Login to the server

   The username is qrc353_2 and password is btPhhy

3. Attach a local copy of a csv file onto the form

4. Click on the Upload button

    QR_353_2's ENCS Server will parse the csv file using strict formatting requirements and re-populate the database as well the procedures. 

    A message will be displayed thereafter with a summary of the commands that were issued, and those that resulted in an error.

## MySQL Features & Calls

#### Registering an account
```
call register_account("<last name>","<middle name>","<first name>",<user id>,<pasword>);
```
- Last name, middle name, and first name cannot be ALL empty/null
- User id and password must be non-negative values

#### Creating an event
```
call create_event("<event name>",<event id>,"<start date>","<end date>", <admin id*>);
```
- Event name cannot be empty
- Event id must be unique
- Event start date must be defined
- Event end date can be empty/null
- Admin id must be an existing user id

    
#### Joining an existing event
```
call join_event(<user id>,<event id>);
``` 
- User id must exist
- Event id must exist

#### Fetching Admins by Names
```
call fetch_admin_names();
``` 

#### Fetching Names of Participants in an Event
```
call fetch_names_in_event(<event id>);
``` 
- Event id must be valid


## Notes

- The project has been tested and has been configured to function over the group account server. For local server testing, the source code in file upload.php needs to be changed.

- Concerns have been addressed by various files. For example, file Errors.sql defines the current error messages that are possible when running a procedure. Procedures are found in the file Procedures.sql. These procedures make calling queries simpler. It also acts as the interface between the Database and the php file. 

