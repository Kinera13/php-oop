<?php
session_start();
require 'Database.php';

class User
{
    public function get_one(){
        //создаём экземпляр класса Database()
        $db = new Database(HOST, USER, PASSWORD, DB, PORT);
        //выполняем метод get_one_db()
        return $db->get_one_db();
    }

    public function get_body_user(){
        //выводим в таблице логин и e-mail авторизированного пользователя
        echo '<h1 text align="center">Редактирование профиля</h1></br>';
        echo '<table style="margin: 0 auto" border="1" width="500">';
        echo '<tr>';
        echo '<form action="" method="post">';
        echo '<td><label for="name">Имя</label></br>';
        echo '<input type="text" name="name" value="'.$_SESSION['name'].'"></td></br>';
        echo '<td><label for="email">E-mail</label></br>';
        echo '<input type="email" name="email" value="'.$_SESSION['email'].'"></td></br>';
        echo '<tr><td><label for="password">Пароль</label></br>';
        echo '<input type="password" name="password" value="'.$_SESSION['password'].'"></td></tr></br>';
        echo '<td><input type="submit" name="update" value="Изменить" ></td>';
        echo '<td><input type="submit" name="delete" value="Удалить"></td>';
        echo '</form>';
        echo '</tr>';
        echo '</table>';

    }

}
//создаём экземпляр класса user
$user = new User();
//выполняем методы get_one() и get_body_user()
$user->get_one();
$user->get_body_user();

