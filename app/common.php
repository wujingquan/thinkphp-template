<?php
// 应用公共文件

function success_json($data = '', $code = 0, $msg = '') {
  return json([
    'code' => $code,
    'msg' => $msg,
    'data' => $data
  ]);
}

function fail_json($data = '', $code = 1, $msg = '') {
  return json([
    'code' => $code,
    'msg' => $msg,
    'data' => $data
  ]);
}