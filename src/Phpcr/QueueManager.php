<?php

namespace PrBuilder\Phpcr;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    const BUILD_QUEUE = 'pr-builder-build';

    private $amqpConnection;

    /**
     * @param SessionInterface $session
     * @param AMQPConnection $amqpConnection
     */
    public function __construct(AMQPConnection $amqpConnection)
    {
        $this->amqpConnection = $amqpConnection;
    }

    public function queueMessage($queueName, array $message)
    {
        $channel = $this->getChannel($queueName);
        $message = new AMQPMessage(json_encode($message));
        $channel->basic_publish($message, '', $queueName);
    }

    public function consume($queueName, $callback)
    {
        $channel = $this->getChannel($queueName);
        $channel->basic_consume($queueName, '', false, true, false, false, function ($msg) use ($callback) {
            $msg = json_decode($msg->body, true);
            return call_user_func_array($callback, array($msg));
        });

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    private function getChannel($queueName)
    {
        $channel = $this->amqpConnection->channel();
        $channel->queue_declare($queueName, false, false, false, false);

        return $channel;
    }
}

