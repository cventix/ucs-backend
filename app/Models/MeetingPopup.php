<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MeetingPopup extends Pivot
{
    public function meetings()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function popups()
    {
        return $this->belongsTo(Popup::class);
    }
}
