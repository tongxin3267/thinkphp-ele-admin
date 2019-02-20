<?php
namespace app\service\model\rbac;

use app\service\validate\rbac\RuleValidate;
use think\Model;

/**
 * 规则模型
 */
class RuleModel extends Model
{
    protected $table = 'pg_auth_rule';

    protected $pk = 'id';

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
        $validate = new RuleValidate;

        if (!$validate->scene($scene)->check($data)) {
            exception($validate->getError());
        }
    }

    /**
     * 添加规则
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
    public function edit(int $id, array $params)
    {
        try {
            $info = $this->getInfo($id);
            unset($params['id']);
            $info->save($params);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 删除规则
     * @param string|int $id
     */
    public function deleteRule($id)
    {
        try {
            $info = $this->getInfo($id);

            // 检查是否有子规则
            $res = $this->getChild($info->id);
            
            if (!empty($res)) {
                exception('请删除子规则');
            }
            
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
     * 获取规则详情
     * @param int|string $id
     *
     */
    public function getInfo($id)
    {
        $res = $this->getOrFail($id);

        return $res;
    }

    /**
     * 获取子规则
     * @param string|int $pid
     * @return array
     */
    protected function getChild($pid)
    {
        $data = $this->order('pid asc')->select();
        $category = new \lib\Category(array('id','pid','title','cname'));
        $data = $category->getChild($pid, $data);

        return $data;
    }

    /**
     * 把返回的数据集转换成Tree
     * access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * return array
     */
    public function getRuleTree($list, $pk='id', $pid = 'pid', $child = 'children', $root=0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $list[$key]['label'] = $data['title'];
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }

        
        return $tree;
    }
}
