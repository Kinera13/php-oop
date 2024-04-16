<?php
require 'Database.php';

class editUser
{
    public function edit_user()
    {
        //создаём экземпляр класса Database()
        $db = new Database(HOST, USER, PASSWORD, DB, PORT);
        //выполняем метод get_edit_db()
        return $db->get_edit_db();
    }
}
//создаём экземпляр класса editUser
$edit = new editUser();
//выполняем метод edit_user()
$edit->edit_user();

