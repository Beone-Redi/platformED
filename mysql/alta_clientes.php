use ener;

drop table if exists en_user, en_card, en_funds, en_log, en_layout_anchor;

create table en_users
(
	ide bigint auto_increment,
    usuario varchar(250),
    email varchar(250),
    company varchar(250),
    fullname varchar(250),
    address text,
    ciudad varchar(200),
    zip int(6),
    aboutme text,
    picture varchar(30),
    idcard varchar(16),
    perfil varchar(50),
    up_date date,
    active int,
    idkey varchar(50),
    primary key (ide),
    index (ide) 
);

create table en_card
(
	ide bigint auto_increment,
    idcard varchar(16),
    company varchar(250),
    ciudad varchar(200),
    picture varchar(30),
    up_date date,
    active int,
    primary key (ide),
    index (ide) 
);

create table en_funds
(
	ide bigint auto_increment,
    company varchar(250),
    ciudad varchar(200),
    picture varchar(30),
    fund decimal(20,2),
    up_date date,
    active int,
    primary key (ide),
    index (ide) 
);

create table en_log
(
	ide bigint auto_increment,
    idecompany varchar(250),
    fund decimal(20,2),
    idtype varchar(50),
    type_description text,
    up_date date,
    active int,
    primary key (ide),
    index (ide) 
);

create table en_layout_anchor
(
	ide bigint auto_increment,
    idecompany varchar(250),
    idcard varchar(16),
    fund decimal(20,2),
    idtype varchar(50),
    up_date date,
    primary key (ide),
    index (ide) 
);