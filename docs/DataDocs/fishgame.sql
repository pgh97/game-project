CREATE DATABASE fishgame default CHARACTER SET UTF8;

use fishgame;

-- 기획 데이터 테이블 생성 쿼리

create table weather_info_data (
    weather_code int not null auto_increment primary key,
    temperature_code int not null,
    wind_code int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table temperature_info_data (
    temperature_code int not null auto_increment primary key,
    min_temperature int not null,
    max_temperature int not null,
	change_time int not null,
	change_value int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table wind_info_data (
    wind_code int not null auto_increment primary key,
    min_wind int not null,
    max_wind int not null,
	change_time int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table tide_info_data (
    tide_code int not null auto_increment primary key,
    high_tide_time1 time null,
    low_tide_time1 time null,
    high_tide_time2 time null,
    low_tide_time2 time null,
	water_splash_time int not null,
	appear_probability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table map_info_data (
    map_code int not null auto_increment primary key,
    map_name varchar(100) not null,
    max_depth int not null,
    min_level int not null,
    distance int not null,
	money_code int not null,
	departure_price int not null,
    departure_time int not null,
    per_durability int not null,
	map_fish_count int not null,
    fish_size_probability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table map_tide_data (
    map_tide_code int not null auto_increment primary key,
    map_code int not null,
    tide_code int not null,
    tide_sort int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table map_fish_data (
    map_fish_code int not null auto_increment primary key,
    map_code int not null,
    fish_code int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table map_item_data (
    map_item_code int not null auto_increment primary key,
    map_code int not null,
    item_code int not null,
    item_type int not null,
    item_probability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table grade_info_data (
    grade_code int not null auto_increment primary key,
    grade_name varchar(100) not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fish_info_data (
    fish_code int not null auto_increment primary key,
    fish_name varchar(100) not null,
    min_depth int not null,
    max_depth int not null,
    min_size int not null,
    max_size int not null,
    fish_probability int not null,
    fish_durability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fish_grade_data (
	fish_grade_code int not null auto_increment primary key,
    fish_code int not null,
    grade_code int not null,
    min_value int not null,
    max_value int not null,
    add_experience int not null,
    money_code int not null,
	min_price int not null,
	max_price int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fishing_item_info_data (
    item_code int not null auto_increment,
    item_name varchar(100) not null,
    weight int null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    primary key(item_code)
);

create table fishing_rod_grade_data (
    item_grade_code int not null auto_increment primary key,
    item_code int not null,
    item_type int not null,
    grade_code int not null,
    durability int not null,
    suppress_probability int not null,
    hooking_probability int not null,
	max_weight int null,
	min_weight int null,
    money_code int not null,
    item_price int not null,
    max_upgrade int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fishing_line_grade_data (
    item_grade_code int not null auto_increment primary key,
    item_code int not null,
    grade_code int not null,
    durability int not null,
    suppress_probability int not null,
    hooking_probability int not null,
	max_weight int null,
	min_weight int null,
    money_code int not null,
    item_price int not null,
    max_upgrade int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fishing_needle_grade_data (
    item_grade_code int not null auto_increment primary key,
    item_code int not null,
    grade_code int not null,
    suppress_probability int not null,
    hooking_probability int not null,
    money_code int not null,
    item_price int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fishing_bait_grade_data (
    item_grade_code int not null auto_increment primary key,
    item_code int not null,
    grade_code int not null,
    fish_probability int not null,
    money_code int not null,
    item_price int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table fishing_reel_grade_data (
    item_grade_code int not null auto_increment primary key,
    item_code int not null,
    grade_code int not null,
    durability int not null,
    reel_number int null,
    reel_winding_amount int null,
    money_code int not null,
    item_price int not null,
    max_upgrade int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table function_info_data (
    function_code int not null auto_increment,
    function_name varchar(200) not null,
    function_probability int null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    primary key(function_code)
);

create table fishing_item_function_data (
    item_grade_code int not null,
    function_code int not null,
    item_type int not null,
    primary key(item_grade_code, function_code)
);

create table fishing_item_upgrade_data (
    upgrade_code int not null auto_increment primary key,
    item_grade_code int not null,
    item_type int not null,
    upgrade_level int not null,
    upgrade_item_code int not null,
    upgrade_item_count int not null,
    money_code int not null,
    upgrade_price int not null,
    add_probability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table ship_item_upgrade_data (
    upgrade_code int not null auto_increment primary key,
    ship_code int not null,
    upgrade_level int not null,
    money_code int not null,
    upgrade_price int not null,
    add_fuel int not null,
    add_probability int not null,
    upgrad_probability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table item_repair_info_data (
    repair_code int not null auto_increment primary key,
    item_code int not null,
    item_type int not null,
    money_code int not null,
    repair_price int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table compensation_info_data (
    compensation_code int not null auto_increment primary key,
    item_code int not null,
    item_type int not null,
    compensation_value int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table quest_info_data (
    quest_code int not null auto_increment primary key,
    quest_type int not null,
    quest_goal int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table quest_compensation_data (
    quest_code int not null,
    compensation_code int not null,
    primary key(quest_code, compensation_code)
);

create table user_level_info_data (
    level_code int not null auto_increment primary key,
    level_experience int not null,
    max_fatigue int not null,
    auction_profit int not null,
    inventory_count int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table money_info_data (
    money_code int not null auto_increment primary key,
    money_name varchar(100) not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table shop_info_data (
    shop_code int not null auto_increment primary key,
    item_code int not null,
    item_type int not null,
    sale_percent int null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table ship_info_data (
    ship_code int not null auto_increment primary key,
    ship_name varchar(100) not null,
    durability int not null,
    fuel int not null,
    max_upgrade int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table upgrade_item_data (
    upgrade_item_code int not null auto_increment primary key,
    upgrade_item_name varchar(100) not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table country_info_data (
    country_code int not null auto_increment primary key,
    country_name varchar(100) not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table language_info_data (
    language_code int not null auto_increment primary key,
    language_name varchar(100) not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table buff_info_data (
    buff_code int not null auto_increment primary key,
    buff_name varchar(100) not null,
    add_buff int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

-- 인게임 데이터 테이블 생성 쿼리

create table account_info (
    account_code int not null primary key,
    account_type int not null,
    hive_code int null,
    account_id varchar(200) not null,
    account_pw varchar(200) not null,
    country_code int not null,
    language_code int not null,
    last_login_date timestamp null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    unique index(account_id)
);

create table account_delete_info (
    account_code int not null primary key,
    account_type int not null,
    hive_code int null,
    account_id varchar(200) not null,
    country_code int not null,
    language_code int not null,
    delete_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    unique index(account_id)
);

create table user_info (
    user_code int not null auto_increment primary key,
    account_code int not null,
    user_nicknm varchar(100) not null,
    level_code int not null,
    user_experience int not null,
    money_gold int not null,
    money_pearl int not null,
    fatigue int not null,
    use_inventory_count int not null,
    use_save_item_count int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table user_weather_history (
    weather_history_code int not null auto_increment primary key,
    user_code int not null,
    weather_code int not null,
    map_wave_code int not null,
	temperature int not null,
    wind int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table user_ship_info (
    user_code int not null,
    ship_code int not null,
    durability int not null,
    fuel int not null,
    upgrade_code int not null,
    upgrade_level int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    primary key(user_code, ship_code)
);

create table user_choice_item_info (
    choice_code int not null auto_increment primary key,
    user_code int not null,
    fishing_rod_code int not null,
    fishing_line_code int not null,
    fishing_needle_code int not null,
    fishing_bait_code int not null,
    fishing_reel_code int not null,
    fishing_item_code1 int null,
    fishing_item_code2 int null,
    fishing_item_code3 int null,
    fishing_item_code4 int null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table user_fish_dictionary (
    map_fish_code int not null,
    user_code int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    primary key(map_fish_code, user_code)
);

create table user_inventory_info (
    inventory_code int not null auto_increment primary key,
    user_code int not null,
    item_code int not null,
    item_type int not null,
    upgrade_code int null,
    upgrade_level int null,
    item_count int not null,
    item_durability int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table user_fish_inventory_info (
    fish_inventory_code int not null auto_increment primary key,
    user_code int not null,
    map_code int not null,
    fish_grade_code int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table user_gitf_box_info (
    box_code int not null auto_increment primary key,
    item_code int not null,
    item_type int not null,
    item_count int not null,
    read_status int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);

create table auction_ranking (
    week_date int not null,
    user_code int not null,
    money_code int not null,
    price_sum int not null,
    auction_rank int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP,
    primary key(week_date, user_code)
);

create table auction_info_data (
    auction_code int not null auto_increment primary key,
    user_code int not null,
    fish_grade_code int not null,
    money_code int not null,
    auction_price int not null,
    change_time int not null,
    create_date timestamp not null DEFAULT CURRENT_TIMESTAMP
);