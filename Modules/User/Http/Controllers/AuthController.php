<?php

namespace Modules\User\Http\Controllers;

use App\Traits\ResponseJSON;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Traits\ForgetResetPasswords;
use Modules\User\Traits\LoginUsers;
use Modules\User\Traits\LogoutUsers;
use Modules\User\Traits\RegisterUsers;
use Modules\User\Traits\SocialLogin;
use Modules\User\Traits\VerifyEmails;

class AuthController extends Controller
{
    use ResponseJSON;

    use LoginUsers, SocialLogin, LogoutUsers, RegisterUsers, ForgetResetPasswords, VerifyEmails;
}
