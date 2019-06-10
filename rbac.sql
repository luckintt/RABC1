drop table `userroles`;
drop table `rolepermissions`;
/*drop table `userpermissions`;*/
drop table `users`;
drop table `roles`;
drop table `permissions`;

create table `users`(
  `ID` integer not null primary key auto_increment,
  `Username` varchar(128) not null,
  `Password` varchar(128) not null
);

create table `roles` (
  `ID` integer not null primary key auto_increment,
  `Rolename` varchar(128) not null,
  `Rolelevel` varchar(128) not null
);

create table `permissions` (
  `ID` integer not null primary key auto_increment,
  `Permission` varchar(128) not null
);

create table `userroles`(
  `ID` integer not null primary key auto_increment,
  `Uid` integer not null,
  `Rid` integer not null,
  foreign key(`Uid`) references users(`ID`),
  foreign key(`Rid`) references roles(`ID`)
);

create table `rolepermissions` (
  `ID` integer not null primary key auto_increment,
  `Rid` integer not null,
  `Pid` integer not null,
  foreign key(`Rid`) references roles(`ID`),
  foreign key(`Pid`) references permissions(`ID`)
);
/*
create table `userpermissions` (
  `ID` integer not null primary key auto_increment,
  `Uid` integer not null,
  `Pid` integer not null,
  foreign key(`Uid`) references users(`ID`),
  foreign key(`Pid`) references permissions(`ID`)
);
*/
/*
 * Insert Initial Table Data
 */

insert into `users`(`Username`,`Password`) values ('张三','zhangsan');
insert into `users`(`Username`,`Password`) values ('李四','lisi');
insert into `users`(`Username`,`Password`) values ('王五','wangwu');
insert into `users`(`Username`,`Password`) values ('admin','admin');


insert into `roles`(`Rolename`,`Rolelevel`) values ('Project_Member',1);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Programmer',2);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Test_Engineer',2);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Programmer_Leader',3);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Project_Supervisor',3);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Test_Engineer_Leader',3);
insert into `roles`(`Rolename`,`Rolelevel`) values ('Admin',10);
/*
insert into `roles`(`Rolelevel`) values (3);
insert into `roles`(`Rolelevel`) values (2);
insert into `roles`(`Rolelevel`) values (1);
*/

/*Project_Member*/
insert into `permissions`(`Permission`) values ('美工');
insert into `permissions`(`Permission`) values ('需求调研');
/*Programmer*/
insert into `permissions`(`Permission`) values ('系统分析');
insert into `permissions`(`Permission`) values ('软件开发');
/*Test_Engineer*/
insert into `permissions`(`Permission`) values ('单元测试');
insert into `permissions`(`Permission`) values ('系统测试');
/*Programmer_Leader*/
insert into `permissions`(`Permission`) values ('确定项目立项');
insert into `permissions`(`Permission`) values ('明细项目计划');
insert into `permissions`(`Permission`) values ('归档项目文档');
/*Project_Supervisor*/
insert into `permissions`(`Permission`) values ('验收项目成果');
/*Test_Engineer_Leader*/
insert into `permissions`(`Permission`) values ('用例评审');
insert into `permissions`(`Permission`) values ('缺陷回归测试');

insert into `userroles`(`Uid`,`Rid`) values (1,1);
insert into `userroles`(`Uid`,`Rid`) values (2,2);
insert into `userroles`(`Uid`,`Rid`) values (3,3);
insert into `userroles`(`Uid`,`Rid`) values (4,7);


insert into `rolepermissions`(`Rid`,`Pid`) values (1,1);
insert into `rolepermissions`(`Rid`,`Pid`) values (1,2);
insert into `rolepermissions`(`Rid`,`Pid`) values (2,3);
insert into `rolepermissions`(`Rid`,`Pid`) values (2,4);
insert into `rolepermissions`(`Rid`,`Pid`) values (3,5);
insert into `rolepermissions`(`Rid`,`Pid`) values (3,6);
insert into `rolepermissions`(`Rid`,`Pid`) values (4,7);
insert into `rolepermissions`(`Rid`,`Pid`) values (4,8);
insert into `rolepermissions`(`Rid`,`Pid`) values (4,9);
insert into `rolepermissions`(`Rid`,`Pid`) values (5,10);
insert into `rolepermissions`(`Rid`,`Pid`) values (6,11);
insert into `rolepermissions`(`Rid`,`Pid`) values (6,12);
/*
insert into `userpermissions`(`Uid`,`Pid`) values (1,1);
insert into `userpermissions`(`Uid`,`Pid`) values (1,2);
insert into `userpermissions`(`Uid`,`Pid`) values (2,1);
insert into `userpermissions`(`Uid`,`Pid`) values (2,2);
insert into `userpermissions`(`Uid`,`Pid`) values (2,3);
insert into `userpermissions`(`Uid`,`Pid`) values (2,4);
insert into `userpermissions`(`Uid`,`Pid`) values (3,1);
insert into `userpermissions`(`Uid`,`Pid`) values (3,2);
insert into `userpermissions`(`Uid`,`Pid`) values (3,5);
insert into `userpermissions`(`Uid`,`Pid`) values (3,6);
insert into `userpermissions`(`Uid`,`Pid`) values (4,1);
insert into `userpermissions`(`Uid`,`Pid`) values (4,2);
insert into `userpermissions`(`Uid`,`Pid`) values (4,3);
insert into `userpermissions`(`Uid`,`Pid`) values (4,4);
insert into `userpermissions`(`Uid`,`Pid`) values (4,5);
insert into `userpermissions`(`Uid`,`Pid`) values (4,6);
insert into `userpermissions`(`Uid`,`Pid`) values (4,7);
insert into `userpermissions`(`Uid`,`Pid`) values (4,8);
insert into `userpermissions`(`Uid`,`Pid`) values (4,9);
insert into `userpermissions`(`Uid`,`Pid`) values (4,10);
insert into `userpermissions`(`Uid`,`Pid`) values (4,11);
insert into `userpermissions`(`Uid`,`Pid`) values (4,12);
*/

select Username,Rolename from users u1,roles r1 ,userroles ur
	where u1.ID=ur.Uid  and r1.ID=ur.Rid  and u1.ID not in ( 
		select  u2.ID from users u2,roles r2 ,userroles ur2  /*找出所有等级比自己高的用户ID*/
			where u2.ID=ur2.Uid and r2.ID=ur2.Rid and r2.Rolelevel>=5)
	order by Username;

select Rolename,r.ID from roles r,users u,userroles ur where Rolelevel=(
select max(`Rolelevel`) from roles where ID in (                                
	select distinct Rid from userroles where Uid in (
           select ID from users where Username="王五"))) and Username="王五" and r.ID=ur.Rid and u.ID=ur.Uid;

insert into `users`(`Username`,`Password`) values ('kk','kk');
insert into `users`(`Username`,`Password`) values ('jj','jj');



select ID,Rolename from roles where Rolelevel<5 and ID not in(select Rid from userroles);






select Username,Rolename,Rolelevel,r1.ID from users u1,roles r1 ,userroles ur
	            where    r1.ID=ur.Rid  and u1.ID not in ( 
		          select  u2.ID from users u2,roles r2 ,userroles ur2  /*找出所有等级比自己高的用户ID*/
			        where u2.ID=ur2.Uid and r2.ID=ur2.Rid and r2.Rolelevel>=4)
			    order by Rolename;













select distinct `Permission` from `permissions` where `Permission`='readfile1' and `ID` in(
	select distinct `Pid` from `rolepermissions` where `Rid`in (
		select `Rid` from `userroles` where Uid=(
			select `ID` from `users` where `Username`='admin')));

select distinct `Permission` from `permissions` where `ID` in(
	select `Pid` from `userpermissions` where `Uid`=2);
    
select distinct `Permission` from `permissions` where `ID` in(
	select distinct `Pid` from `rolepermissions` where `Rid`in (
		select distinct `ID` from `roles` where `Rolelevel` < (
			select max(`Rolelevel`) from `roles` where `ID`=(
				select distinct `Rid` from `userroles` where `Uid`=3))
			or `ID`=(select distinct max(`Rid`) from `userroles` where `Uid`=3)));


select `Username`,`Rolelevel` from `users`,`roles`,`userroles` where `users`.ID=`userroles`.Uid 
	and `roles`.`ID`=`userroles`.`Rid` and `roles`.`ID` in(
		select `ID` from `roles` where `roles`.`Rolelevel` < 3) ;
        
/*选出角色等级比自己低的用户，如果一个用户有多个角色就只考虑最高级别*/
select Username , Rolename,r1.ID from users u1,roles r1 ,userroles ur
	where u1.ID=ur.Uid  and r1.ID=ur.Rid  and u1.id not in ( 
		select  u2.id from users u2,roles r2 ,userroles ur2
			where u2.ID=ur2.Uid and r2.ID=ur2.Rid and r2.rolelevel>=5);

select max(`Rolelevel`),`ID` from `roles` where `ID`in(
				select distinct `Rid` from `userroles` where `Uid`=4);

select `ID` from `roles` where `roles`.`Rolelevel` < 3;

UPDATE `rbac`.`permissions` SET `Permission`='Read_File3' WHERE `ID`='5';