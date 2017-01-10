<?php

namespace AppBundle\Services;

use Maknz\Slack\Client;

/**
 * Class SlackClient.
 */
class SlackClient
{
    /**
     * @var string
     */
    private $botName;

    /**
     * @param $botName
     */
    public function __construct($botName)
    {
        $this->botName = $botName;
    }

    /**
     * @param $webhookUrl
     *
     * @return Client
     */
    private function createClient($webhookUrl)
    {
        return new Client($webhookUrl, [
            'username' => $this->botName,
        ]);
    }

    /**
     * @param $webhookUrl
     * @param $message
     */
    public function send($webhookUrl, $message)
    {
        try {
            $client = $this->createClient($webhookUrl);
            $client->send($message);
        } catch (\Exception $e) {
        }
    }
}
