
<p>Your reset code: {{ $verificationCode }}</p>

<p>
    <a href="{{ sprintf(
    '%s/reset-password?verification_code=%s',
    config('platform.spa_links.frontend.forget_password'),
    $verificationCode
    ) }}"><button>Reset</button></a>
</p>
