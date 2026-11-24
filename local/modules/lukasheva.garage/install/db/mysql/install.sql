create table garage_cars (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	CONTACT_ID INTEGER,
	CARMAKE VARCHAR(50),
	MODEL VARCHAR(50),
	LICENSE_PLATE_NUMBER VARCHAR(50),
	YEAR_OF_MANUFACTURE VARCHAR(50),
	CAR_COLOR VARCHAR(50),
	MILEAGE VARCHAR(50)
);

insert into garage_cars (CONTACT_ID, CARMAKE, MODEL, LICENSE_PLATE_NUMBER, YEAR_OF_MANUFACTURE, CAR_COLOR, MILEAGE) values
	(1, 'Toyota',     'Camry',   'А123ВС77', '2018', 'Белый',      '87500'),
	(1, 'Volkswagen', 'Polo',    'К456ОР99', '2020', 'Серебристый', '54200'),
	(2, 'Kia',        'Rio',     'М789ТХ77', '2019', 'Чёрный',      '112300'),
	(3, 'Skoda',      'Octavia', 'Е321НК50', '2021', 'Синий',       '38900'),
	(3, 'Hyundai',    'Solaris', 'С654УА78', '2017', 'Красный',     '145000'),
	(1, 'Kia',        'Rio',     'М789ТХ77', '2019', 'Чёрный',      '112300'),
	(1, 'Skoda',      'Octavia', 'Е321НК50', '2021', 'Синий',       '38900'),
	(1, 'Hyundai',    'Solaris', 'С654УА78', '2017', 'Красный',     '145000');

create table garage_cars_makes (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	MAKE VARCHAR(50)
);

insert into garage_cars_makes (MAKE) values
	('Toyota'),
	('Volkswagen'),
	('Kia'),
	('Skoda'),
	('Hyundai');

create table garage_cars_models (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	MAKE_ID INTEGER,
	MODEL VARCHAR(50)
);

insert into garage_cars_models (MAKE_ID, MODEL) values
	(1, 'Camry'),
	(1, 'RAV4'),
	(2, 'Polo'),
	(2, 'Golf'),
	(2, 'Passat'),
	(3, 'Rio'),
	(3, 'Sportage'),
	(4, 'Octavia'),
	(4, 'Fabia'),
	(5, 'Solaris'),
	(5, 'Elantra'),
	(5, 'Sonata');

create table garage_zajavky (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	DATE_CREATE DATETIME,
	CREATED_BY_ID INTEGER,
	DEAL_ID INTEGER,
	STATUS_ZAJAVKY VARCHAR(50),
	TYPE_ZAJAVKY VARCHAR(50)
);

create table garage_zajavky_detail (
	ID INTEGER NOT NULL auto_increment PRIMARY KEY,
	ZAJAVKA_ID INTEGER,
	PRODUCT_ID INTEGER,
	QUANTITY INTEGER
);