<?php
namespace app\admin\controller;

use app\service\model\AdminUserModel;
use app\service\model\rbac\GroupModel;
use app\service\model\rbac\GroupAccessModel;

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
        $this->groupAccessModel = new GroupAccessModel();
    }

    public function list()
    {
        $subsql = $this->groupAccessModel->group('uid')->field('uid, GROUP_CONCAT(group_id) as groups')->buildSql();
        $users = $this->userModel->alias('A')
            ->where('A.admin_id', '<>', config('auth.auth_super_id'))
            ->rightJoin([$subsql => 'G'], ' G.uid = A.admin_id')
            ->select();

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
    public function update($id)
    {
        try {
            $res = $this->userModel->updateUser($id, $this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 删除用户
     */
    public function delete($id)
    {
        try {
            $res = $this->userModel->deleteUser($id);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }
}
