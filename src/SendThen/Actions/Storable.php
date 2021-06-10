<?php


namespace SendThen\Actions;


use SendThenException;

trait Storable
{
    /**
     * @return mixed
     * @throws SendThenException
     */
    public function save()
    {
        return ($this->exists()) ? $this->update() : $this->insert();
    }

    /**
     * @return mixed
     * @throws SendThenException
     */
    public function insert()
    {
        $action = 'add';
        if(property_exists($this, 'createAction'))
        {
            $action = $this->createAction;
        }

        $result = $this->connection()->post($this->getEndpoint() . '.' . $action, $this->jsonWithNamespace());

        return $this->selfFromResponse($result);
    }


    /**
     * @return $this
     * @throws SendThenException
     */
    public function update()
    {
        $result = $this->connection()->post($this->getEndpoint() . '.update', $this->jsonWithNamespace());
        return $this;
    }

    /**
     * @return bool|mixed
     * @throws SendThenException
     */
    public function remove()
    {
        $action = 'delete';
        if (property_exists($this, 'deleteAction')) {
            $action = $this->deleteAction;
        }

        $result = $this->connection()->post($this->getEndpoint() . '.' . $action, $this->jsonWithNamespace());
        if ($result === 204) {
            return true;
        }

        return $result;
    }
}