<?php
session_start();
require 'Database.php';
class index
{
    public $text;
    public function get_all(){
        //создаём новый объект класса Database()
        $db = new Database(HOST, USER, PASSWORD, DB, PORT);
        //выполняем метод get_all_db()
        return $db->get_all_db();
    }


    public function get_body($text){
        //выводим в таблице логины и e-mail`ы всех пользователей из базы данных через цикл foreach
        echo '<h1 text align="center">Все пользователи</h1>';
        echo '<table style="margin: 0 auto">';
        echo '<tr>';
            echo '<td><a href="?name">Имя</a></td>';
            echo '<td><a href="?email">E-mail</a></td>';
        echo '</tr>';
        foreach ($text as $item){
            echo '<tr>';
            echo "<td>{$item['name']}</td>";
            echo "<td>{$item['email']}</td>";
            //если логин из сессии совпадает с одним из логинов из списка, то рядом в ним вывом ссылку на страницу с личным кабинетом пользователя
            if ($_SESSION['name'] === $item['name']) {
                print "<td><a href='User.php?id=".$item['id']."'>&#9997</a> </td>";
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    public function get_form(){
        //выводим форму для авторизацмм
        echo '<h4>Авторизация</h4>';
        echo '<form action="auth.php" method="post" >';
        echo '<label for="name">Имя</label></br>';
        echo '<input type="text" name="name" placeholder="Имя"></br>';
        echo '<label for="password">Пароль</label></br>';
        echo '<input type="password" name="password" placeholder="Пароль"></br>';
        echo '<input type="submit" value="Отправить">';
        echo '</form>';
        echo '<a href="addUser.php">Вы ещё не зарегистрированы? Тогда вам сюда!</a>';
    }

}
//создаём экземпляр класса штвуч
$index = new Index();
//выполяем методы get_all(), get_body($text) и get_form()
$text = $index->get_all();
$index->get_body($text);
$index->get_form();
