use qrc353_2;

drop table
    if exists error;
drop procedure
    if exists signal_error;

create table error
(
    code    int primary key,
    message varchar(255)
);

insert into error
values (1, 'User ID already exists.'),
       (2, 'User ID does not exist.'),
       (3, 'User ID or Password cannot be a negative value.'),
       (4, 'User ID is already a participant of event ID.'),
       (100, 'First, Middle, and Last names cannot be empty.'),
       (101, 'Event ID already exists.'),
       (102, 'Event ID does not exist.'),
       (103, 'Event name cannot be empty.'),
       (104, 'Illegal event start/end date format.');

create procedure signal_error(error_code int)
begin
    set @error_message =
            (
                select concat(message)
                from error
                where error.code = error_code
            );

    signal sqlstate '45000'
        set message_text = @error_message;
end;

alter table error add unique index (code);