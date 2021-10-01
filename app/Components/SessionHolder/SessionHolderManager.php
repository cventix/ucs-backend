<?php


namespace App\Components\SessionHolder;


use App\Components\SessionHolder\Contracts\Driver;
use App\Components\SessionHolder\Drivers\GoogleMeet;
use App\Components\SessionHolder\Drivers\ZoomMeeting;
use App\Components\SessionHolder\Drivers\ZoomWebinar;
use Illuminate\Support\Manager;

class SessionHolderManager extends Manager
{

    /**
     * @inheritDoc
     */
    public function getDefaultDriver()
    {
        return null;
    }

    /**
     * @return ZoomMeeting
     */
    public function createZoomMeetingDriver() {
        return new ZoomMeeting();
    }

    /**
     * @return ZoomWebinar
     */
    public function createZoomWebinarDriver() {
        return new ZoomWebinar();
    }

    /**
     * @return GoogleMeet
     */
    public function createGoogleMeetDriver() {
        return new GoogleMeet();
    }

    /**
     * @param null $driver
     * @return Driver|null
     */
    public function driver($driver = null)
    {
        $driver = $this->createDriver(
            $driver ?: $this->getDefaultDriver()
        );
        if ($driver) {
            return $driver;
        }
    }
}
