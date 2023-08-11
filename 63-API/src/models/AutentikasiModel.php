<?php
namespace MyApp\Models;

use MyApp\Core\Database;
use PDO;

class AutentikasiModel extends Database
{
  public function __construct()
  {
    parent::__construct();
    $this->setTableName('autentikasi');
    $this->setColumn([
      'id',
      'email',
      'password'
    ]);
  }

  public function insert($data)
  {
    $table = [
      'email' => $data['email'],
      'password' => password_hash($data['password'], PASSWORD_BCRYPT)
    ];
    return $this->insertData($table);
  }

  public function getByEmail($email)
  {
    return $this->get(['email' => $email])->fetch(PDO::FETCH_ASSOC);
  }

}