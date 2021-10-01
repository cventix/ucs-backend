
<p>Your reset code: {{ $verificationCode }}</p>

<p>
    <a href="{{ sprintf(
    '%s/reset-password?verification_code=%s&email=%s',
    config('platform.spa_links.frontend.forget_password'),
    $verificationCode,
    $email
    ) }}"><button>Reset</button></a>
</p>
