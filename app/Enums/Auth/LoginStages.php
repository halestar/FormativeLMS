<?php

namespace App\Enums\Auth;

use App\Traits\EnumToArray;

enum LoginStages: string
{
    use EnumToArray;
    case PromptEmail = 'prompt_email';
    case PromptMethod = 'prompt_method';
    case PromptPassword = 'prompt_password';
    case CodeVerification = 'code_verification';
    case CodeTimeout = 'code_timeout';
    case ResetPassword = 'reset_password';
    case LockedUser = 'locked_user';
    case BlockedLogin = 'blocked_login';
}
