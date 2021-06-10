<?php


namespace SendThen\Actions;


use SendThen\Actions\Attributes\Filter;
use SendThen\Actions\Attributes\Page;
use SendThen\Actions\Attributes\Sort;
use SendThenException;

trait FindAll
{
    /**
     * @param Filter|null $filter
     * @param Page|null $page
     * @param Sort|null $sort
     * @return mixed
     * @throws SendThenException
     */
    public function get(Filter $filter = null, Page $page = null, Sort $sort = null)
    {
        $attributes = [
            'filter' => $filter,
            'page' => $page,
            'sort' => $sort,
        ];

        $result = $this->connection()->get($this->getEndpoint(), array_filter($attributes));

        return $this->collectionFromResult($result);
    }

    /**
     * @param Filter|null $filter
     * @param Sort|null $sort
     * @return mixed
     * @throws SendThenException
     */
    public function getAll(Filter $filter = null, Sort $sort = null)
    {
        $attributes = [
            'filter' => $filter,
            'sort' => $sort,
        ];

        $result = $this->connection()->get($this->getEndpoint(), array_filter($attributes), true);

        return $this->collectionFromResult($result);
    }
}