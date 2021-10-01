<?php


namespace App\Components\SessionHolder\Contracts;


use App\Components\SessionHolder\Exceptions\InvalidSessionHolderConfiguration;
use App\Models\User;
use Carbon\Carbon;

abstract class Driver
{
    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var User
     */
    protected $host;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Carbon
     */
    protected $startTime;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var array
     */
    protected $meta = [];

    protected abstract function _register();
    protected abstract function _update();
    protected abstract function _delete();

    /**
     * @param string $sessionId
     * @return Driver
     */
    public function sessionId(string $sessionId): Driver
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @param User $host
     * @return Driver
     */
    public function host(User $host): Driver
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param Carbon $startTime
     * @return Driver
     */
    public function at(Carbon $startTime) : Driver
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @param int $duration
     * @return Driver
     */
    public function duration(int $duration): Driver
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @param string $title
     * @return Driver
     */
    public function title(string $title): Driver
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param array $meta
     * @return Driver
     */
    public function meta(array $meta): Driver
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return mixed
     * @throws InvalidSessionHolderConfiguration
     */
    public function register() {
        if (!$this->host) throw new InvalidSessionHolderConfiguration('host');
        if (!$this->startTime) throw new InvalidSessionHolderConfiguration('start_time');
        if (!$this->duration) throw new InvalidSessionHolderConfiguration('duration');

        return $this->_register();
    }

    /**
     * @return mixed
     * @throws InvalidSessionHolderConfiguration
     */
    public function update() {
        if (!$this->sessionId) throw new InvalidSessionHolderConfiguration('session_id');
        if (!$this->startTime) throw new InvalidSessionHolderConfiguration('start_time');
        if (!$this->duration) throw new InvalidSessionHolderConfiguration('duration');

        return $this->_update();
    }

    /**
     * @return mixed
     * @throws InvalidSessionHolderConfiguration
     */
    public function delete() {
        if (!$this->sessionId) throw new InvalidSessionHolderConfiguration('session_id');

        return $this->_delete();
    }

}
