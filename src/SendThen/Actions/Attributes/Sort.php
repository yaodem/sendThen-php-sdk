<?php


namespace SendThen\Actions\Attributes;


class Sort implements \JsonSerializable
{
    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    private const POSSIBLE_ORDER = [
        self::ORDER_ASC,
        self::ORDER_DESC
    ];

    protected array $fillable;
    private array $sorts;
    private $singleFieldSort;

    public function __construct(array $sorts = [], bool $singleFieldSort = false)
    {
        $this->fill($sorts);
        $this->singleFieldSort = $singleFieldSort;
    }

    /**
     * @param string $field
     * @param string $order
     * @return Sort|string[]
     */
    public function addSort(string $field, string $order)
    {
        if($this->singleFieldSort)
        {
            throw new \LogicException('You can only sort one item when singleFieldSort is set to true');
        }

        return ($this->isFillable($field) && $this->isValidOrder($order))
            ? $this->sorts[] =
                [
                    'field' => $field,
                    'order' => $order
                ]
            : $this;
    }

    /**
     * @return array
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @return mixed
     */
    public function getSingleFieldSort()
    {
        return $this->singleFieldSort;
    }


    /**
     * @return array|false|mixed
     */
    public function jsonSerialize()
    {
        return ($this->singleFieldSort) ? reset($this->sorts) : $this->getSorts();
    }

    /**
     * @param string $field
     * @return bool
     */
    protected function isFillable(string $field): bool
    {
        return !(count($this->fillable) > 0) || in_array($field, $this->fillable);
    }

    /**
     * @param string $order
     * @return bool
     */
    protected function isValidOrder(string $order): bool
    {
        return in_array($order, self::POSSIBLE_ORDER);
    }

    /**
     * @param array $sorts
     */
    protected function fill(array $sorts)
    {
        foreach ($sorts as $field => $order)
        {
            $this->addSort($field, $order);
        }
    }
}