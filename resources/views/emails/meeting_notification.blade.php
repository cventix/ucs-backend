<?php

/**
 * @var \App\Models\Meeting $meeting
 * @var \App\Models\User $user
 */
?>

<p>{{ __("Dear")}} {{ $user->full_name }},</p>
<p>{{ __("You have a meeting session at")}} {{ $jalaliDate }}.</p>
<p>Join: {{ route('meetings.join', ['meeting' => $meeting->id, 'user' => $user->id, 'tenant' => tenant()->id]) }}</p>