<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Amqp
{
    private $connection;
    private $channel;
    private $sData = [];

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection($_ENV['RABBIT_HOST'], $_ENV['RABBIT_PORT'], $_ENV['RABBIT_USER'], $_ENV['RABBIT_PASSWORD']);
        $this->channel = $this->connection->channel();
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function send($lead)
    {
        $this->channel->queue_declare($_ENV['RABBIT_QUEUE'], false, true, false, false);

        $res = new AMQPMessage(json_encode($lead));
        $this->channel->basic_publish($res, '', 'work');
    }

    /**
     * @throws ErrorException
     */
    public function receive()
    {
        $this->channel->queue_declare($_ENV['RABBIT_QUEUE'], false, true, false, false);
        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($res) {
            $res = (object)json_decode($res->body);
            foreach($res as $key => $data) {
                $date = date("d.m.Y H:i:s");
                $logMessage = $data->id . " | " . $data->categoryName . " | " . $date;
                @file_put_contents("logs/log.txt", PHP_EOL . $logMessage, FILE_APPEND);
            }
        };

        $this->channel->basic_consume($_ENV['RABBIT_QUEUE'], '', false, true, false, false, $callback);
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }

    }

    /**
     * @param $data
     * @throws Exception
     */
    public function separation($data)
    {
        array_push($this->sData, $data);
        if(count($this->sData) == 100) {
            $this->send($this->sData);
            $this->sData = [];
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

}