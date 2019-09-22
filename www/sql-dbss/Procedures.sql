use qrc353_2;

drop procedure if exists AccountRegister;
drop procedure if exists EventCreate;
drop procedure if exists EventJoin;


create procedure AccountRegister(l_name varchar(20),
                                 f_name varchar(20),
                                 m_name varchar(20),
                                 uID int,
                                 pass int)
begin
    declare msg varchar(255);
    declare var1 varchar(255);
    declare exit handler for 1062
        select 'User ID ', uID, ' is not available.' as message;


    set var1 = concat(l_name, m_name, f_name);

    if var1 is null or var1 = ''
    then
        set msg = 'First, Middle, and Last names are empty.';
    elseif uID < 0 or pass < 0
    then
        set msg = 'User ID or Password is a negative value.';
    end if;

    if msg is not null
    then
        signal sqlstate '45000'
            set message_text = msg;
    end if;

    insert into Account
    values (l_name, f_name, m_name, uID, pass);
end;


create procedure EventCreate(name varchar(20),
                             eID int,
                             s_date varchar(10),
                             e_date varchar(10),
                             uID int)
begin

    declare var1 bool;
    declare msg varchar(255);
    declare dat1,dat2 date;
    declare exit handler for 1062
        select 'Event ID ', eID, ' is not available.' as message;

    set var1 = exists
        (
            select Account.userID
            from Account
            where Account.userID = uID
        );

    if not var1
    then
        set msg = concat('User ID ', uID, ' does not exist.');
    elseif name = ''
    then
        set msg = 'Event name is empty.';
    elseif s_date is null
        or s_date = ''
        or not regexp_like(s_date, '[0-9]{4}-[0-9]{2}-[0-9]{2}')
    then
        set msg = 'Event start date format not YYYY-MM-DD.';
    elseif length(e_date) > 1 and
           not regexp_like(e_date, '[0-9]{4}-[0-9]{2}-[0-9]{2}')
    then
        set msg = 'Event end date format not YYYY-MM-DD.';
    end if;

    if msg is not null then
        signal sqlstate '45000' set message_text = msg;
    end if;

    if e_date = '' or e_date is null
    then
        set e_date = '9999-12-31';
    end if;


    set dat1 = str_to_date(s_date, '%Y-%m-%d');
    set dat2 = str_to_date(e_date, '%Y-%m-%d');

    insert into Event
    values (name, eID, dat1, dat2, uID);

    insert into Role
    values (uID, eID);
end;

create procedure EventJoin(uID int, eID int)
begin
    declare var1,var2,var3 int;
    declare msg varchar(255);

    set var1 = exists
        (
            select Account.userID
            from Account
            where Account.userID = uID
        );

    set var2 = exists
        (
            select Event.EventID
            from Event
            where Event.EventID = eID
        );

    set var3 = exists
        (
            select Role.userID
            from Role
            where Role.userID = uID
              and Role.eventID = eID
        );

    if not var1
    then
        set msg = concat('User ID ', uID, ' does not exist.');
    elseif not var2
    then
        set msg = concat('Event ID ', eID, ' does not exist');
    elseif var3
    then
        set msg = concat('User ID ', uID, ' is already a participant of event ID ', eID);
    end if;

    if msg is not null
    then
        signal sqlstate '45000' set message_text = msg;
    else
        insert into Role
        values (uID, eID);
    end if;
end;

