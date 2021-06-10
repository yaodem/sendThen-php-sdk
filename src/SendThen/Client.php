<?php


namespace SendThen;


use SendThen\Entities\Balance;
use SendThen\Entities\BulkSms;
use SendThen\Entities\Group;
use SendThen\Entities\SenderId;
use SendThen\Entities\Template;
use SendThen\Connection;



class Client
{
    protected Connection $connection;

    /**
     * Client constructor.
     * @param Connection $client
     */
    public function __construct(Connection $client)
    {
        $this->connection = $client;
    }

    /**
     * @param array $attributes
     * @return Balance
     */
    public function balance(array $attributes = []): Balance
    {
        return new Balance($this->connection, $attributes);
    }

    /**
     * @param array $attributes
     * @return Group
     */
    public function group(array $attributes = []): Group
    {
        return new Group($this->connection, $attributes);
    }

    /**
     * @param array $attributes
     * @return SenderId
     */
    public  function senderId(array $attributes = []): SenderId
    {
        return new SenderId($this->connection, $attributes);
    }

    /**
     * @param array $attributes
     * @return Template
     */
    public function template(array $attributes = []): Template
    {
        return new Template($this->connection, $attributes);
    }

    /**
     * @param array $attributes
     * @return BulkSms
     */
    public function sms(array $attributes = []): BulkSms
    {
        return new BulkSms($this->connection, $attributes);
    }
}