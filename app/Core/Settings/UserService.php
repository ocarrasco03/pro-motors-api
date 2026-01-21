<?php

namespace App\Core\Settings;

interface UserService
{
    public function getUsers();
    public function getUser(int $id);
    public function createUser(array $data);
    public function updateUser(int $id, array $data);
    public function deleteUser(int $id);
}
