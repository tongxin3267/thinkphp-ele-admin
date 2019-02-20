<?php
namespace app\service\model\rbac;

use app\service\validate\rbac\GroupValidate;
use think\Model;

/**
 * 用户分组
 */
class GroupModel extends Model
{
    protected $table = 'pg_auth_group';

    protected $pk = 'id';

    public function setRulesAttr($value)
    {
        return implode(',', $value);
    }

    /**
     * 各类事件
     */
    public static function init()
    {
        self::event('before_insert', function ($user) {
            self::validate($user, 'create');
        });

        self::event('before_update', function ($user) {
            self::validate($user, 'edit');
        });

        self::event('before_delete', function ($user) {
            self::validate($user, 'delete');
        });
    }

    /**
     * 数据验证
     * @param array $data
     * @param string $scene
     */
    public static function validate($data, $scene)
    {
        $validate = new GroupValidate;

        if (!$validate->scene($scene)->check($data)) {
            exception($validate->getError());
        }
    }

    /**
     * 添加
     * @param array $params
     */
    public function add(array $params)
    {
        try {
            $this->save($params);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 修改规则
     * @param array $params
     */
    public function edit(array $params)
    {
        try {
            $info = $this->getInfo($params['id']);
            unset($params['id']);
            $info->save($params);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 删除
     * @param string|int $id
     */
    public function deleteGroup($id)
    {
        try {
            $info = $this->getInfo($id);

            $info->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取树形结构
     *
     */
    public function getTree()
    {
        $data = $this->order('pid asc')->select();
        $category = new \lib\Category(array('id','pid','title','cname'));
        $res = $category->getTree($data);//获取分类数据树结构
        return $res;
    }

    /**
     * 获取分组详情
     * @param int|string $id
     *
     */
    public function getInfo($id)
    {
        $res = $this->getOrFail($id);

        return $res;
    }
}
