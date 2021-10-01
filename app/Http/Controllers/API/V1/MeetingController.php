<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MeetingControllerTrait;
use App\Models\Meeting;
use App\Traits\CRUDActions;

class MeetingController extends Controller
{
    use CRUDActions, MeetingControllerTrait;

    protected function beforeShowEntity(Meeting $meeting)
    {
        $this->authorize('view', $meeting);
    }
}
