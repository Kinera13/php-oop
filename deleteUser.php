<?php
require 'Database.php';

class deleteUser
{
    public function delete_user()
    {
        //создаём экземпляр класса Database()
        $db = new Database(HOST, USER, PASSWORD, DB);
        //выполняем метод get_delete_db
        return $db->get_delete_db();
    }

}
//создаём объект экземпляр deleteUser()
$delete = new deleteUser();
//выпролняем метод delete_user
$delete->delete_user();
