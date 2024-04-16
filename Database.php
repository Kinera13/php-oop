<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Заносим данные для подключения к базе данных в константы
define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', 'root');
define('DB', 'agatech');
define('PORT', '3307');
class Database
{
    public $db;
    //Создаём и проверяем подключение к базе данных
    public function __construct($host, $db, $user, $password, $port){
        $this->db = new PDO("mysql:host=localhost; dbname=agatech; port=3307", 'root', 'root');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!$this->db){
            exit('No connection this database');
        }

    }
    //запрашиваем данные из базы данных и зависимости от выбранного поля сортируем их в прямом порядке
    public function get_all_db(){
        if (isset($_GET['email'])) {
            $sql = "SELECT * FROM users ORDER BY email ASC";
        }
        else {
            $sql = "SELECT * FROM users ORDER BY name ASC ";
        }
        $res = $this->db->query($sql);
        if (!$res){
            return FALSE;
        }
        //потом выводим в цикле

            $row = $res->fetchAll(PDO::FETCH_ASSOC);

       return $row;


    }
    //Разносим содержимое суперглобального массива $_POST по переменным
    public function get_auth_db(){
        $name = $_POST['name'];
        $password = $_POST['password'];
        //запрашиваем данные из базы данных, используя данные, пришедшие из глобального массива POST

        $sql = $this->db->prepare("SELECT password FROM users WHERE name= :name ");
        $sql->execute([':name'=>$name]);
        $value = $sql->fetchAll(PDO::FETCH_ASSOC);
        //Сравниваем хеш из базы данных с хешированными данными из поля $_POST['password'] и в случае успеха происходит запись логина в суперглобальный массив $_SESSION и  редирект на траницу index.php
        if (password_verify($password, $value[0]['password'])){
            $_SESSION['name'] = $name;
            header('Location: index.php');


        }
        //Иначе получаем сообщение о неуспехе и ссылку на страницу index.php
        else {
            echo 'Пароль неправильный.';
            echo '<a href="index.php">Вернуться на главную</a>';
        }
    }

    public function get_reg_user_db()
    {
        //Проверяем нажатие кнопки
        if (isset($_POST['sibmit'])) {
            //Разносим содержимое суперглобального массива $_POST по переменным и экранируем специальные символы, если они приходят
            $name =  $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            //хешируем пароль
            $password = password_hash($password, PASSWORD_DEFAULT);
            //делаем запрос к базе данных
            $sql_name = $this->db->prepare("SELECT * FROM users WHERE name = :name ");
            $result = $sql_name->execute(['name'=>$name]);
            //проверяем на соответствие регулярному выражению
            if (!preg_match("/^[a-zа-яё0-9-_]{2,20}$/iu", $name)) {
                $name_error = "Имя должно содержать только буквы русского и латинского алфавитов и пробел.Длина логина от 2 до 20 символов (включительно).";
            }
            else{
                $name_error = null;
            }
            //проверяем, является ли введёная строка реальным электронным адресом
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = "Пожалуйста, введите действительный адрес электронной почты";
            }
            else{
                $email_error = null;
            }
            //Проверяем длину пароля
            if (strlen($password) < 6) {
                $password_error = "Пароль должен состоять минимум из 6 символов";
            }
            else{
                $password_error = null;
            }
            //Если всё верно, то делаем новую запись в базу данных, логин и пароль записываем в гобальный массив $_SESSION и делаем редирект на index.php
            if($name_error == null && $email_error==null && $password_error == null){
                $sql = $this->db->prepare("INSERT INTO users ( name, email, password) VALUES (:name, :email, :password)");
                $res = $sql->execute(['name'=> $name, 'email' => $email, 'password' => $password]);
              //$value = mysqli_fetch_assoc($res);
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                header('Location: index.php');
            }
            //иначе выводим ошибки
            else {
                echo $name_error.'</br>';
                echo $email_error.'</br>';
                echo $password_error.'</br>';
            }
        }

    }

    public function get_one_db(){
        //записываем значение из суперглобального массива $_GET в переменную
        $id = $_GET['id'];
        //на основании этого значения запрашиваем нужную запись из базы данных
        $sql = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $sql->execute([':id'=>$id]);
        $value = $sql->fetch();
        //записываем значение логина и e-mail в суперглобальный массив $_SESSION
        $_SESSION['name'] = $value['name'];
        $_SESSION['email'] = $value['email'];
        $_SESSION['password'] = $value['password'];
        //отслеживем, какая кнопка была нажата
        //если delete, то удаляем запись из базы данный
        if (isset($_POST['delete'])){
            $this->get_delete_db();
        }
        //иначе - редактируем*
        elseif(isset($_POST['update'])){
            $this->get_edit_db();
        }
        return $value;
    }

    public function get_edit_db(){
        //Разносим содержимое суперглобального массива $_POST по переменным
        if (isset($_POST['update'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            //записываем значение из суперглобального массива $_GET в переменную
            $id = $_GET['id'];
            //делаем запрос из базы данных по паролю
            $my = $this->db->prepare("SELECT password FROM users WHERE password = :password");
            $my->execute([':password' => $password]);
            $result = $my->fetch(PDO::FETCH_ASSOC);
            //проверяем, было ли изменено содержание поля "пароль"
            if ($password!=$result['password']){
                //если да, то экранируем специальные символы и хешируем пароль
                //$password = mysqli_real_escape_string($this->db, $password);
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
            //проверяем поля на пустоту
            //если все поля заполнены, то выполняем запрос на изменение записи в базе данных
            if ($name != "" && $email != "" && $password != "") {
                //делаем запрос на изменение существующей в базе данных записи
                $sql = $this->db->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id");
                $res = $sql->execute([':name' => $name, ':email' => $email, ':password' => $password, ':id' => $id]);
                //записываем логин в суперглобальный массив $_SESSION
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                //делаем редирект на страницу index.php
                header('Location: index.php');
            }
            //иначе выволим сообщение об ошибке
            else {
                echo "Все поля должны быть заполнены!";
            }


        }
    }

    public function get_delete_db(){
        if (isset($_POST['delete'])) {
            //записываем значение из суперглобального массива $_GET в переменную
            $id = $_GET['id'];
            //делаем запрос на удаление записи из базы данных
            $sql = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $res = $sql->execute([':id' => $id]);
            header('Location: index.php');
        }
    }

}