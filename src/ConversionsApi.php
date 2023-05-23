<?php

namespace Esign\ConversionsApi;

use Esign\ConversionsApi\Collections\EventCollection;
use Esign\ConversionsApi\Objects\DefaultUserData;
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequestAsync;
use FacebookAds\Object\ServerSide\UserData;
use GuzzleHttp\Promise\PromiseInterface;

class ConversionsApi
{
    protected EventCollection $events;
    protected UserData $userData;
    protected $pixel_id;
    protected $test_code;

    public function __construct($access_token = NULL)
    {
        $this->events = new EventCollection();
        $this->setUserData(DefaultUserData::create());
        $token = $access_token ? $access_token : config('conversions-api.access_token');
        Api::init(null, null, $token, false);
    }

    public function setUserData(UserData $userData): self
    {
        $this->userData = $userData;

        return $this;
    }

    public function getUserData(): UserData
    {
        return $this->userData;
    }

    public function addEvent(Event $event): self
    {
        $this->events->push($event);

        return $this;
    }

    public function addEvents(iterable $events): self
    {
        $this->events = $this->events->merge($events);

        return $this;
    }

    public function setEvents(iterable $events): self
    {
        $this->events = new EventCollection($events);

        return $this;
    }

    public function getEvents(): EventCollection
    {
        return $this->events;
    }

    public function clearEvents(): self
    {
        return $this->setEvents([]);
    }

    public function setPixelId($pixel_id): self
    {
        $this->pixel_id = $pixel_id;
        return $this;
    }
    
    public function setTestCode($test_code): self
    {
        $this->test_code = $test_code;
        return $this;
    }

    public function sendEvents(): PromiseInterface
    {
        $pixelId = $this->pixel_id ? $this->pixel_id : config('conversions-api.pixel_id');
        $eventRequest = (new EventRequestAsync($pixelId))
            ->setEvents($this->events);

        $testCode = $this->test_code ? $this->test_code : config('conversions-api.test_code');
        if ($testCode) {
            $eventRequest->setTestEventCode($testCode);
        }

        return $eventRequest->execute();
    }
}
