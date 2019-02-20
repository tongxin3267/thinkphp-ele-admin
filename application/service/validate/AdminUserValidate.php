<?php
namespace app\service\validate;

use think\Validate;

/**
 * 后台用户验证类
 */
class AdminUserValidate extends Validate
{
    protected $rule = [
        'admin_user'      =>  'require|unique:admin',
        'admin_password'   =>  'require',
        'group_id'   =>  'require',
    ];

    protected $message  =   [
        'admin_user.require'    => '账号必须',
        'admin_user.unique' => '账号重复',
        'admin_password.require' => '密码必须',
        'group_id.require' => '用户组必须',
    ];

    protected $scene = [
        'edit'    =>  ['admin_user', 'admin_password', 'group_id', 'admin_id'],
        'create'  =>  ['admin_user', 'admin_password', 'group_id'],
        'delete'  =>  ['admin_id']
    ];
}
