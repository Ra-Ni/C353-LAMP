use qrc353_2;

drop procedure
    if exists register_account;
drop procedure
    if exists create_event;
drop procedure
    if exists join_event;


create procedure register_account(last_name varchar(20),
                                  first_name varchar(20),
                                  middle_name varchar(20),
                                  user_id int,
                                  user_password int)
begin
    declare exit handler
        for 1062
        call signal_error(1);

    set @full_name = concat(last_name, middle_name, first_name);

    if @full_name is null
        or @full_name = ''
    then
        call signal_error(100);

    elseif user_id < 0
        or user_password < 0
    then
        call signal_error(3);

    else
        insert into account
        values (last_name, first_name, middle_name, user_id, user_password);
    end if;
end;


create procedure create_event(name varchar(20),
                              event_id int,
                              start_date varchar(10),
                              end_date varchar(10),
                              user_id int)
begin
    declare exit handler for 1062
        call signal_error(101);
    declare exit handler for 1141
        call signal_error(104);

    if @date_template is null
    then
        set @date_template = '%Y-%m-%d';
        set @empty_date = str_to_date('', @date_template);
        set @maximum_date = str_to_date('9999-12-31',@date_template);
    end if;

    set @a_start_date = str_to_date(start_date, @date_template);
    set @a_end_date = str_to_date(end_date, @date_template);

    if not exists
        (
            select _user_id
            from account
            where _user_id = user_id
        )
    then
        call signal_error(2);

    elseif name = ''
    then
        call signal_error(103);

    elseif @a_start_date is null
        or @a_start_date = @empty_date
        or day(@a_start_date) = '0'
        or month(@a_start_date) = '0'
    then
        call signal_error(104);
    end if;

    if @a_end_date is null
        or @a_end_date = @empty_date
    then
        set @a_end_date = @maximum_date;
    end if;

    insert into event
    values (name, event_id, @a_start_date, @a_end_date, user_id);

    insert into role
    values (user_id, event_id);
end;

create procedure join_event(user_id int, event_id int)
begin
    if not exists
        (
            select _user_id
            from account
            where _user_id = user_id
        )
    then
        call signal_error(2);

    elseif not exists
        (
            select _event_id
            from event
            where _event_id = event_id
        )
    then
        call signal_error(102);

    elseif exists
        (
            select _user_id
            from role
            where _user_id = user_id
              and _event_id = event_id
        )
    then
        call signal_error(4);
    end if;

    insert into role
    values (user_id, event_id);
end;