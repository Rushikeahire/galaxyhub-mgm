<?php

namespace App\Http\Middleware;

use App\Action\Group\Group;
use Closure;
use Cog\Laravel\Ban\Http\Middleware\ForbidBannedUser as ForbidBannedUserAlias;
use Illuminate\Http\Request;

class ForbidBannedUser extends ForbidBannedUserAlias
{
    public function handle($request, Closure $next)
    {
        $user = $this->auth->user();
        $groups = $user->groups()->get();
        $isNotMember = true;

        if (count($groups) > 0) { // 미가입 유저는 권한 자체가 없다.
            $isNotMember = $groups->every(function ($value, $key) {
                return match ($value->group_id) {
                    Group::ARMA_REJECT, Group::ARMA_DEFER, Group::ARMA_APPLY => true,
                    default => false,
                };
            });
        }

        if ($isNotMember) {
            return redirect()->route('home');
        }

        return parent::handle($request, $next);
    }
}
