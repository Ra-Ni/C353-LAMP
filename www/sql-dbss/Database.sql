use qrc353_2;

drop table if exists Account,Event,Role;


create table Account
(
    lastname   varchar(20),
    firstname  varchar(20),
    middlename varchar(20),
    userID     int not null primary key,
    password   int not null
);

create table Event
(
    Event       varchar(20),
    EventID     int  not null primary key,
    start_date  date not null,
    end_date    date,
    AdminUserID int  not null
);

create table Role
(
    userID  int not null,
    eventID int not null
);