<?php


namespace SendThen\Actions\Attributes;


final class Page implements \JsonSerializable
{

    private const DEFAULT_SIZE = 100;
    private const DEFAULT_NUMBER = 1;

    private ?int $number;
    private ?int $size;

    /**
     * Page constructor.
     * @param int|null $size
     * @param int|null $number
     */
    public function __construct(?int $size = self::DEFAULT_SIZE, ?int $number = self::DEFAULT_NUMBER)
    {
        $this->size = $this->isValidNumber($size) ? $size : self::DEFAULT_SIZE;
        $this->number = $this->isValidNumber($number) ? $number : self::DEFAULT_NUMBER;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     *
     */
    public function next()
    {
        ++$this->number;
    }

    public function previous()
    {
        --$this->number;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getPage();
    }

    /**
     * @param int|null $number
     * @return bool
     */
    private function isValidNumber(?int $number): bool
    {
        return $number >= 1;
    }

    /**
     * @return array
     */
    public function getPage()
    {
        return [
            'size' => $this->size,
            'number' => $this->number
        ];
    }
}