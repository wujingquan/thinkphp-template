<?php

declare(strict_types=1);

namespace app\middleware;

use Exception;
use think\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Request;

/**
 * 后台登录跳转判断，未登录跳转到登录页面
 * Class Auth
 * @package app\middleware
 */
class Auth
{
  public function handle($request, \Closure $next, $option = [])
  {
    if (!empty($option['whiteList'])) {
      $url = $request->url();
      foreach ($option['whiteList'] as $v) {
        if ($v === $url) {
          return $next($request);
        }
      }
    }

    $token = $request->header('Authorization');
    if ($token) {
      $token = str_replace('Bearer ', '', $token);
    }
    if (!$token) {
      return json([
        'code' => 1,
        'msg' => '未登录'
      ]);
    }
    try {
      $user = $this->decode($token);
      $request->user = $user->ext;
    } catch (\Firebase\JWT\ExpiredException $e) {
      // 过期
      return json([
        'code' => 1,
        'msg' => '未登录'
      ]);
    } catch (\Firebase\JWT\SignatureInvalidException $e) {
      // 校验不通过
      return json([
        'code' => 1,
        'msg' => '未登录'
      ]);
    } catch (Exception $e) {
      return json([
        'code' => 1,
        'msg' => '未登录'
      ]);
    }

    return $next($request);
  }

  /**
   * 中间件结束调度
   * @param Response $response
   */
  public function end(Response $response)
  {
  }

  public static function decode($jwt)
  {
    return JWT::decode($jwt, new Key(md5(env('JWT_SECURIT')), 'HS256'));
  }
}
