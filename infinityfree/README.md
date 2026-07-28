# InfinityFree deployment

This is a PHP/MySQL version of Number Battle for InfinityFree. It uses browser polling every two seconds instead of Socket.IO, because InfinityFree does not run Node.js or WebSockets.

1. Create an InfinityFree account and website, then create a MySQL database in its control panel.
2. Open phpMyAdmin for that database and import `database.sql`.
3. Edit `config.php` with the database host, database name, user and password from the MySQL Databases page.
4. Upload the **contents** of this `infinityfree` folder to the website's `htdocs` folder using File Manager or FTP.
5. Open your InfinityFree domain and create two accounts in separate browsers/devices to play.

Do not commit your filled `config.php` to a public repository.
