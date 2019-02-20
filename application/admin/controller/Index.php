<?php
namespace app\admin\controller;

use app\admin\model\UserInfoModel;

class Index extends Base
{
    public function index()
    {
        return $this->sendSuccess();
    }
}
