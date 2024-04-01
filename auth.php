<?php
session_start();
require 'Database.php';

class auth
{
 public function auth(){
     //если в суперглобальный массив $_SERVER пришли данные из формы
     if ($_SERVER["REQUEST_METHOD"] == "POST") {
         //создаём экземпляр класса Database()
         $db = new Database(HOST, USER, PASSWORD, DB);
         //выполняем метод get_auth_db()
         return $db->get_auth_db();
     }
 }
}
//создаём экземпляр класса auth()
$auth = new Auth;
//$auth->auth();