<?php

namespace app\controller;

use app\BaseController;
use app\model\Admin;

class Register extends BaseController
{
  public function index()
  {
    $username = input('post.username');
    $password = input('post.password');

    $admin = Admin::where('username', $username)
      ->find();

    if ($admin) {
      return json([
        'code' => 1,
        'msg' => '账号已存在'
      ]);
    }

    $salt = bin2hex(random_bytes(10));
    $saltedPassword = $salt . $password;
    $hashedPassword = sha1($saltedPassword);

    $admin = new Admin();
    $admin->username = $username;
    $admin->password = $hashedPassword;
    $admin->salt = $salt;
    $admin->save();

    return json([
      'code' => 0,
      'msg' => '',
      'data' => ''
    ]);
  }
}
