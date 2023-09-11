<?php

namespace app\controller;

use app\BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\model\Admin;

class Login extends BaseController
{
    public function index()
    {
        $username = input('post.username');
        $password = input('post.password');
        
        $admin = Admin::where('username', $username)
            ->find();

        if (!$admin) {
            return json([
                'code' => 1,
                'msg' => '账号或密码错误'
            ]);
        }

        $saltedPassword = $admin->salt . $password;
        $hashedPassword = sha1($saltedPassword);
        if ($admin->password !== $hashedPassword) {
            return json([
                'code' => 1,
                'msg' => '账号或密码错误'
            ]);
        }
        
        
        
        $token = $this->encode($admin);
        return json([
            'code' => 0,
            'msg' => '',
            'data' => [
                'access_token' => $token
            ]
        ]);
    }

    public function encode($admin)
    {
        $payload = [
            'iss' => 'http://example.org', // issuer 签发人
            'aud' => 'http://example.com', // audience 受众
            'iat' => time(), // Issued At 签发时间
            // 'nbf' => 1357000000, // Not Before 生效时间
            // 'exp' => time() + 60 * 3, // expiration time 过期时间
            // 'sub' => '', // subject 主题
            // 'jti' => '1', // JWT ID 编号

            'ext' => [
                'id' => $admin->id
            ]
        ];
        return JWT::encode($payload, md5(env('JWT_SECURIT')),  'HS256');
    }
}
