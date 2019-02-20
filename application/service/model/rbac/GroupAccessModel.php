<?php
namespace app\service\model\rbac;

use app\admin\validate\rbac\GroupValidate;
use think\Model;

/**
 *
 */
class GroupAccessModel extends Model
{
    protected $table = 'pg_auth_group_access';
}
