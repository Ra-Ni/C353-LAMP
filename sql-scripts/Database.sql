use qrc353_2;


drop table if exists event,role,account;


create table account
(
    _last_name     varchar(20),
    _first_name    varchar(20),
    _middle_name   varchar(20),
    _user_id       int not null primary key,
    _user_password int not null
);

create table event
(
    _name          varchar(20),
    _event_id      int  not null primary key,
    _start_date    date not null,
    _end_date      date,
    _admin_user_id int  not null,
    foreign key (_admin_user_id) references account(_user_id)
);

create table role
(
    _user_id  int not null,
    _event_id int not null,
    foreign key (_user_id) references account(_user_id),
    foreign key (_event_id) references event(_event_id)
);

alter table account add unique index (_user_id);
alter table event add unique index (_event_id);
alter table role add unique index(_user_id,_event_id);