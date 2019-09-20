drop procedure if exists RegisterAccount;
drop procedure if exists CreateEvent;
drop procedure if exists JoinEvent;
drop table if exists Person,Event,Role;

create table Person
(
    lastname   varchar(20) not null,
    firstname  varchar(20) not null,
    middlename varchar(20),
    userID     int         not null primary key,
    password   int         not null
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

create procedure RegisterAccount(l_name varchar(20),
                              f_name varchar(20),
                              m_name varchar(20),
                              uID int,
                              pass int)
begin
    if not exists
        (
            select userID
            from Person
            where Person.userID = uID
        )
    then
        insert into Person
        values (l_name, f_name, m_name, uID, pass);
    end if;

    select *
    from Person
    where Person.userID = uID;
end;



create procedure CreateEvent(name varchar(20),
                             eID int,
                             s_date date,
                             e_date date,
                             uID int)
begin
    if
            not exists
                (
                    select Event.EventID
                    from Event
                    where Event.EventID = eID
                )
            and
            exists
                (
                    select Person.userID
                    from Person
                    where Person.userID = uID
                )
    then

        insert into Event
        values (name, eID, s_date,ifnull(e_date,'9999-12-31'), uID);
        insert into Role
        values (uID, eID);
    end if;

    select *
    from Event
    where Event.eventID = eID;
end;

create procedure JoinEvent(userID int,EventID int)
begin
    if
            exists
                (
                    select Person.userID
                    from Person
                    where Person.userID = userID
                )
            and
            exists
                (
                    select Event.EventID, Event.AdminUserID
                    from Event
                    where Event.EventID = EventID
                      and Event.AdminUserID != userID
                )
    then
        insert into Role
        values (userID, eventID);
    end if;

    select *
    from Role
    where Role.userID = userID
      and Role.eventID = eventID;
end;