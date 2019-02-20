<?php
namespace app\admin\controller;

use app\common\HttpResponse;
use app\service\AdminUserService;
use Lcobucci\JWT\Builder;

/**
 *
 */
class Auth extends HttpResponse
{
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->service  = new AdminUserService;
    }

    /**
     * 处理登录
     */
    public function doLogin()
    {
        try {
            $admin_id = $this->service->doLogin($this->params);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendSuccess(['token' => $this->makeSign($admin_id)]);
    }

    /**
     * 生成token
     * @param int $id
     */
    protected function makeSign($uid)
    {
        $token = (new Builder())
            ->setIssuedAt(time())
            ->setExpiration(time() + (86400 * 3))
            ->set('uid', $uid)
            ->getToken();
        $token->getHeaders();
        $token->getClaims();

        return (string) $token;
    }

    public function info($token)
    {
        return $this->sendSuccess(['roles' => ['editor'], 'name' => 'admin', 'avatar' => 'http://thirdwx.qlogo.cn/mmopen/1Jav4ibUmeIaRFCDkxAwO6GhMdYbVoG3GtXqWzGZ8h6ibV83ib8ab2YhtW9GKy0IqJ4rnPyksF8KkENkGEOMPWh3Tz5ibTUEGhFS/132']);
    }

    public function logout()
    {
        return $this->sendSuccess();
    }
}
