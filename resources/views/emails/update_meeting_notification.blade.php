<?php
/**
 * @var \App\Models\Meeting $meeting
 * @var \App\Models\User $user
 */
?>

<p>Dear {{ $user->firstname }} {{ $user->lastname }},</p>
<p>Meeting "{{ $meeting->getTitle() }}" has been updated and new date time is from {{ $meeting->getStartAt() }} until {{ $meeting->getEndAt() }}.</p>
<p>You can access to it via {{ sprintf(
    '%s/meetings/%s/join/%s',
    env('APP_URL'),
    $meeting->getId(),
    $user->getId()
) }}</p>
