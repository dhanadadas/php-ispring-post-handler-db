<?php
//open libs
require "libs/rb.php";
//connect
R::setup( 'mysql:host=127.0.0.1;dbname=db','db', 'root');
if (!R::testConnection()) {
    echo 'нет соединения с базой данных';
}