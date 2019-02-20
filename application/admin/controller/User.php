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

    public function index()
    {
        $users = $this->userModel->field('admin_password', true)->select();

        $this->assign('users', $users->toArray());
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->method() === 'POST') {
            try {
                $res = $this->userModel->add($this->params);
            } catch (\Exception $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendSuccess();
        } else {
            $groups = $this->groupModel->select();
            $this->assign('groups', $groups->toArray());
            return $this->fetch('info');
        }
    }

    /**
     * 编辑用户
     */
    public function edit($admin_id)
    {
        if ($this->request->method() === 'POST') {
            try {
                $res = $this->userModel->edit($this->params);
            } catch (\Exception $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendSuccess();
        }

        $info = $this->userModel->getInfo($admin_id);
        unset($info['admin_password']);

        $ids = $this->userModel->getUserGroupIds($info->admin_id);
        $info->group_id = $ids;

        $groups = $this->groupModel->select();

        $this->assign('groups', $groups->toArray());
        $this->assign('info', $info->toArray());

        return $this->fetch('info');
    }

    /**
     * 删除用户
     */
    public function delete()
    {
        if ($this->request->method() === 'POST') {
            try {
                $res = $this->userModel->deleteUser($this->params);
            } catch (\Exception $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendSuccess();
        }
    }
}
