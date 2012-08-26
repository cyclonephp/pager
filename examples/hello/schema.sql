drop table if exists items;

create table items (
  id int primary key auto_increment,
  name varchar(32),
  price float
);

drop procedure if exists load_items;

delimiter //
create procedure load_items()
begin
  declare i int default 1;
  while i < 55 do
    insert into items(name, price) values (concat('item ', i), round(rand() * 100, 2));
    set i := i + 1;
  end while;
end;
//
delimiter ;

call load_items();