![MCSL Logo](public/MCSL.jpg)
# Installation
1. Install `XAMPP/LAMPP`
2. Install `node/js`
3. Install `composer`
4. download the repository and copy the content to `htdocsc/Yourpath` or `www/Yourpath` directory  
5. import the `database/dumps/First_Commit_ab_No_User` into your MySql/MariaDB database
6. if there is a `database/dumps/Newer_Data_ab.sql` import this file to
7.  run a shell in `htdocs/Yourpath` or `www/Yourpath` and run `npm install`
8. Run a shell in Yourpath and execute `composer install` & `composer update`
9. run this in a shell: `cp .env.example .env`
10. customize the DB values in .env   
**-Example:**

    ```DB_CONNECTION=mysql  
    DB_HOST=127.0.0.1  
    DB_PORT=3306  
    DB_DATABASE=yourdb  
    DB_USERNAME=root  
    DB_PASSWORD=mypasswort```   
12. run `php artisan key:generate` in a shell
13. run `php artisan storage:link` in shell
14. run `npm run dev` & `php artisan serve -host 0.0.0.0 --port 80`
15. resolve all dependencies
16. log in with the values e-mail: `email@example.com` and password: `TE6a9qpQ2pB47eqa8UjY`.
17. have fun
