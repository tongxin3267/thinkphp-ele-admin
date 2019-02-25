<?php
namespace app\service\model;

use think\Model;
use app\service\validate\AdminUserValidate;
use app\service\model\rbac\GroupAccessModel;
use think\Db;

/**
 * 后台用户
 */
class AdminUserModel extends Model
{
    protected $table = 'pg_admin';

    protected $pk = 'admin_id';

    protected $autoWriteTimestamp = true;

    public function setAdminPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 数据验证
     * @param array $data
     * @param string $scene
     */
    public static function validate($data, $scene)
    {
        $validate = new AdminUserValidate;

        if (!$validate->scene($scene)->check($data)) {
            exception($validate->getError());
        }
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
     * 注册用户
     * @param array $params
     */
    public function addUser(array $params)
    {

        Db::startTrans();
        try {
            $groups = \explode(',', $params['groups']);
            $this->save($params);

            $access = new GroupAccessModel;

            $temp = [];
           
            foreach ($groups as $v) {
                $temp[] = ['uid' => $this->admin_id, 'group_id' => $v];
            }

            $access->saveAll($temp);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 修改用户
     * @param array $params
     */
    public function updateUser(int $id, array $params)
    {
        Db::startTrans();
        try {
            $info = $this->getInfo($id);
            unset($params['admin_id']);
            $info->save($params);
            $groups = \explode(',', $params['groups']);

            $this->deleteUserGroup($info->admin_id);
            $access = new GroupAccessModel;
            $temp = [];
            foreach ($groups as $v) {
                $temp[] = ['uid' => $info->admin_id, 'group_id' => $v];
            }

            $access->saveAll($temp);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 删除用户
     * @param array $params
     */
    public function deleteUser($id)
    {
        Db::startTrans();
        try {

            if ($id == config('auth.auth_super_id')) {
                exception('操作出错');
            }

            $info = $this->getInfo($id);

            $access = new GroupAccessModel;
        
            $map = ['uid' => $info->admin_id];
            $access->where($map)->delete();

            $info->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 获取用户信息
     * @param string|array $id
     */
    public function getInfo($map)
    {
        $res = $this->getOrFail($map);
        return $res;
    }

    /**
     * 获取用户信息
     * @param string $userName
     */
    public function getUserInfo($userName)
    {
        $res = $this->where([
            'admin_user' => $userName
        ])->find();

        if (empty($res)) {
            exception('没有此用户！');
        }

        return $res;
    }

    /**
     * 获取用户分组ids
     * @param string $uid
     */
    public function getUserGroupIds($uid)
    {
        $access = new GroupAccessModel;
        
        $ids = $access->where(['uid' => $uid])->column('group_id');

        return $ids;
    }

    /**
     * 删除用户分组关联
     * @param string $uid
     */
    public function deleteUserGroup($uid)
    {
        $access = new GroupAccessModel;
        
        $access->where(['uid' => $uid])->delete();
    }
}
