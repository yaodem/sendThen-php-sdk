<?php


namespace SendThen\Actions\Attributes;


class Filter implements \JsonSerializable
{
    protected array $fillable;
    protected array $filters;

    /**
     * Filter constructor.
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->fill($filters);
    }

    /**
     * @param string $key
     * @param $value
     * @return Filter
     */
    public function addFilter(string $key, $value)
    {
        return ($this->isFillable($key)) ? $this->filters[$key] = $value : $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getFilters();
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    protected function fill(array $filters)
    {
        foreach ($filters as $key => $value)
        {
            $this->addFilter($key, $value);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isFillable(string $key)
    {
        return !(count($this->fillable) > 0) || in_array($key, $this->fillable);
    }
}