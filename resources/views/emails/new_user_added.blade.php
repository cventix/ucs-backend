<?php

/**
 * @var \App\Models\User $user
 */
?>
<p>Dear {{ $user->firstname }} {{ $user->lastname }}</p>
<p>
    Welcome to {{ config('app.name') }}
    Please see our website on {{ config('app.url') }} .

    Login detail:
    email and username: {{ $user->email }}
    password: {{ $password }}
</p>