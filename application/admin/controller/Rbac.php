<?php
namespace app\admin\controller;

use app\service\model\rbac\RuleModel;
use app\service\model\rbac\GroupModel;

class Rbac extends Base
{
    protected $ruleModel;
    
    protected $groupModel;

    public function __construct()
    {
        parent::__construct();
        $this->ruleModel = new RuleModel();
        $this->groupModel = new GroupModel();
    }

    /**
     * 用户组列表
     *
     * @return void
     */
    public function groups()
    {
        $data = $this->groupModel->select();

        $rules = $this->ruleModel->order('pid asc')->field('id,title,pid')->select();
        $tree = $this->ruleModel->getRuleTree($rules->toArray());

        return $this->sendSuccess(['groups' => $data, 'rules' => $tree]);
    }

    /**
     * 添加用户组
     *
     * @return void
     */
    public function addGroup()
    {
        try {
            $res = $this->groupModel->add($this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }


    /**
     * 更新用户组
     *
     * @param integer $id 标识
     * @return void
     */
    public function updateGroup(int $id)
    {
        try {
            $res = $this->groupModel->edit($this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 删除用户组
     *
     * @param integer $id 标识
     * @return void
     */
    public function deleteGroup(int $id)
    {
        try {
            $res = $this->groupModel->deleteGroup($id);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 规则列表
     *
     * @return void
     */
    public function rules()
    {
        $res = $this->ruleModel->getTree();
        return $this->sendSuccess($res);
    }


    /**
     * 添加规则
     *
     * @return void
     */
    public function addRule()
    {
        try {
            $res = $this->ruleModel->add($this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage() . $e->getFile().$e->getLine());
        }

        return $this->sendSuccess();
    }


    /**
     * 更新规则
     *
     * @param integer $id 标识
     * @return void
     */
    public function updateRule(int $id)
    {
        try {
            $res = $this->ruleModel->edit($id, $this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * 删除规则
     *
     * @param integer $id 标识
     * @return void
     */
    public function deleteRule(int $id)
    {
        try {
            $res = $this->ruleModel->deleteRule($id);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess();
    }
}
