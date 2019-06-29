Users hierarchy
========================


Project Goal
--------------

For two arbitrary collection of roles and users in json format, given a user Id returns a list of AlLL its subordinates(i.e: including its subordinates's subordinates).

Example json file.

Role:
```
    [
      {
        "Id": 1,
        "Name": "System Administrator",
        "Parent": 0
      },
      {
        "Id": 2,
        "Name": "Location Manager",
        "Parent": 1
      }
    ]
```    
User:
```
    [
      {
        "Id": 1,
        "Name": "Adam Admin",
        "Role": 1
      },
      {
        "Id": 2,
        "Name": "Emily Employee",
        "Role": 4
      }
    ]
```


Project Tech
--------------
  * PHP
  * Symfony framework
  * Composer
  * PHPUnit testing framework
  
Project Structure
-------------- 
```
app
    Resources           // input json files
src
    AppBundle
        Command         // test command running in console
        Controller      // test command running in console 
        Model           // class user and role        
        Service         // main class of this project
tests                   // phpunit test cases
```

Project Deployment
------------
1. git clone the whole project
2. run `composer install`
3. run demo `bin/console app:demo`
4. run test `./vendor/bin/simple-phpunit`
   
![image](http://github.com/wanbo-xiao/UserRole/raw/master/image/running.png)

![image](http://github.com/wanbo-xiao/UserRole/raw/master/image/testing.png)


