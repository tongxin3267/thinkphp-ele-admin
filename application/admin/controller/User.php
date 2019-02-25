<?php
namespace app\admin\controller;

use app\service\model\AdminUserModel;
use app\service\model\rbac\GroupModel;

/**
 * 后台用户
 */
class User extends Base
{
    protected $groupModel;

    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->groupModel = new GroupModel();
        $this->userModel = new AdminUserModel();
    }

    public function list()
    {
        $users = $this->userModel->field('admin_password', true)->select();

        return $this->sendSuccess($users);
    }

    public function add()
    {
        try {
            $res = $this->userModel->addUser($this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 编辑用户
     */
    public function update($admin_id)
    {
        try {
            $res = $this->userModel->updateUser($admin_id, $this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 删除用户
     */
    public function delete($admin_id)
    {
        try {
            $res = $this->userModel->deleteUser($admin_id);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }
}
