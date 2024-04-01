<?php
//Заносим данные для подключения к базе данных в константы
define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', 'root');
define('DB', 'agatech');
class Database
{
    public $db;
    //Создаём и проверяем подключение к базе данных
    public function __construct($host, $user, $password, $db){
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->db = mysqli_connect($host, $user, $password);
        if(!$this->db){
            exit('No connection this database');
        }
        if(!mysqli_select_db($this->db, $db)){
            exit('No table');
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
        $res = mysqli_query($this->db,$sql);
        if (!$res){
            return FALSE;
        }
        //потом выводим в цикле
        for ($i=0; $i < mysqli_num_rows($res); $i++){
            $row[] = mysqli_fetch_assoc($res);
        }
        return $row;

    }
    //Разносим содержимое суперглобального массива $_POST по переменным
    public function get_auth_db(){
        $name = $_POST['name'];
        $password = $_POST['password'];
        //запрашиваем данные из базы данных, используя данные, пришедшие из глобального массива POST
        $sql = "SELECT password FROM users WHERE name='$name'";
        $res = mysqli_query($this->db,$sql);
        $value = mysqli_fetch_assoc($res);
        //Сравниваем хеш из базы данных с хешированными данными из поля $_POST['password'] и в случае успеха происходит запись логина в суперглобальный массив $_SESSION и  редирект на траницу index.php
        if (password_verify($password, $value['password'])){
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
            $name = mysqli_real_escape_string($this->db, $_POST['name']);
            $email = mysqli_real_escape_string($this->db, $_POST['email']);
            $password = mysqli_real_escape_string($this->db, $_POST['password']);
            //хешируем пароль
            $password = password_hash($password, PASSWORD_DEFAULT);
            //делаем запрос к базе данных
            $sql_name = "SELECT * FROM users WHERE name = name ";
            $result = $res = mysqli_query($this->db, $sql_name);
            //проверяем на соответствие регулярному выражению
            if (!preg_match("/^[a-zа-яё0-9-_]{2,20}$/iu", $name)) {
                $name_error = "Имя должно содержать только буквы русского и латинского алфавитов и пробел.Длина логина от 2 до 20 символов (включительно).";

            }
            //проверяем, является ли введёная строка реальным электронным адресом
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = "Пожалуйста, введите действительный адрес электронной почты";
            }

            //Проверяем длину пароля
            if (strlen($password) < 6) {
                $password_error = "Пароль должен состоять минимум из 6 символов";
            }
            //Если всё верно, то делаем новую запись в базу данных, логин и пароль записываем в гобальный массив $_SESSION и делаем редирект на index.php
            if(!$name_error &&!$email_error &&!$password_error){
                $sql = "INSERT INTO users ( name, email, password) VALUES ('$name', '$email', '$password')";
                $res = mysqli_query($this->db,$sql);
              //$value = mysqli_fetch_assoc($res);
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
                header('Location: index.php');
            }
            //иначе пвыводим ошибки
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
        $sql = "SELECT * FROM users WHERE id = '$id'";
        $res = mysqli_query($this->db, $sql);
        $value = mysqli_fetch_assoc($res);
        //записываем значение логина и e-mail в суперглобальный массив $_SESSION
        $_SESSION['name'] = $value['name'];
        $_SESSION['email'] = $value['email'];
        $_SESSION['password'] = $value['password'];
        //отслеживем, какая кнопка была нажата
        //если delete, то удаляем запись из базы данный
        if (isset($_POST['delete'])){
            $this->get_delete_db();
        }
        //иначе - редактируем
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
            $my = "SELECT password FROM users WHERE password = '$password'";
            $query = mysqli_query($this->db, $my);
            $result = mysqli_fetch_assoc($query);
            //проверяем, было ли изменено содержание поля "пароль"
            if ($password!=$result['password']){
                //если да, то экранируем специальные символы и хешируем пароль
                $password = mysqli_real_escape_string($this->db, $password);
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
            //проверяем поля на пустоту
            //если все поля заполнены, то выполняем запрос на изменение записи в базе данных
            if ($name != "" && $email != "" && $password != "") {
                //делаем запрос на изменение существующей в базе данных записи
                $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = '$id'";
                $res = mysqli_query($this->db, $sql);
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
            $sql = "DELETE FROM users WHERE id = '$id'";
            $res = mysqli_query($this->db, $sql);
            header('Location: index.php');
            session_unset();
        }
    }

}