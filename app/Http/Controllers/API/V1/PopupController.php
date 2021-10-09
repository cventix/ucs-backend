<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingPopup;
use App\Models\Popup;
use App\Traits\CRUDActions;

class PopupController extends Controller
{
    use CRUDActions;

    public function getMeetings(Popup $popup)
    {
        $meetings = $popup->meetings;
        
        return $this->successResponse($meetings);
    }

    public function postMeeting(Popup $popup, Meeting $meeting)
    {
        $meetingPopup = MeetingPopup::create([
            'popup_id' => $popup->id,
            'meeting_id' => $meeting->id,
        ]);

        return $this->successResponse();
    }
}
