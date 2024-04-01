<?php
session_start();
require 'Database.php';

class addUser
{
    public function get_reg_user(){
        //если в суперглобальный массив $_SERVER пришли данные из формы
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
       //создаём экземпляр класса Database
        $db = new Database(HOST, USER, PASSWORD, DB);
        return $db->get_reg_user_db();
        }
    }



    public function get_body_user(){
        //выводим форму для регистрации пользователя
        echo '<h1 align="center">Регистрация</h1>';
        echo '<form action="addUser.php" method="post">';
        echo '<label for="name">Имя</label></br>';
        echo '<input type="text" name="name" placeholder="Имя"></br>';
        echo '<label for="email">E-mail</label></br>';
        echo '<input type="email" name="email" placeholder="e-mail"></br>';
        echo '<label for="password">Пароль</label></br>';
        echo '<input type="password" name="password" placeholder="Пароль"></br>';
        echo '<input type="submit" name="sibmit" value="Отправить">';
        echo '</form>';

    }

}
//создаём экземпляр класса addUser
$addUser = new AddUser;
//выполняем методы get_reg_user() и get_body_user()
$addUser->get_reg_user();
$addUser ->get_body_user();
