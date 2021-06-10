<?php


namespace SendThen\Actions;


use SendThenException;

trait FindById
{
    /**
     * @return bool
     * @throws SendThenException
     */
    public function findById(): bool
    {
        $result = $this->connection()->post($this->getEndpoint(), $this->jsonWithNamespace());

        if($result === 200)
        {
            return true;
        }

        return  $this->selfFromResponse($result);
    }
}