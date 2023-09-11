<?php
declare (strict_types = 1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use app\validate\Common;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    protected $model = null;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    public function index() {
        return $this->list();
    }

    public function create() {
        
    }

    public function read($id) {
        $item = $this->model::where('id', $id)->find();
        return success_json($item);
    }

    public function edit($id) {

    }

    public function update($id) {
        
    }

    public function delete($id) {
        $this->model::destroy($id);
        return success_json();
    }

    public function list()
    {
        $data = input('');
        validate(Common::class)->check($data);
        $page = input('?get.page') && intval(input('get.page')) >= 1 ? intval(input('get.page')) : 1;
        $limit = input('?get.limit') && intval(input('get.limit')) > 0 ? intval(input('get.limit')) : 10;
        $total = $this->model::count();
        $items = $this->model::page($page, $limit)->select();
        return json([
            "page" => $page,
            "limit" => $limit,
            "total" => $total,
            "items" => $items,
        ]);
    }


}
