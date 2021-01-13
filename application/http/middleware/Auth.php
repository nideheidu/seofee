<?php

namespace app\http\middleware;

use think\facade\Request;
use app\common\library\Config;

class Auth
{
    public function handle($request, \Closure $next)
    {
        Config::config();
        if (config('param.admin_allow_ip')) {
            if (!in_array($request->ip(), explode(',', config('param.admin_allow_ip')))) {
                throw new HttpException(401, 'Forbidden');

            }

        }
        app()->rbac = Rbac::instance();
        app()->user = Rbac::instance()->user();
        if (!Rbac::instance()->notNeedLogin()) {
            Rbac::instance()->user() || $this->redirect('public/login');
            Rbac::instance()->check() || $this->error('您无权限操作');

        }
        return $next($request);

    }
}