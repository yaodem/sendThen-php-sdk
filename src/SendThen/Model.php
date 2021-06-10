<?php


namespace SendThen;


use JsonSerializable;
use SendThen\Entities\Balance;
use SendThen\Entities\BulkSms;
use SendThen\Entities\Group;
use SendThen\Entities\SenderId;
use SendThen\Entities\Template;
use SendThenException;
use StdClass;

abstract class Model implements JsonSerializable
{
    const NESTING_TYPE_ARRAY_OF_OBJECTS = 0;
    const NESTING_TYPE_NESTED_OBJECTS = 1;

    protected array  $references = [
        Group::TYPE => Group::class,
        Balance::TYPE => Balance::class,
        Template::TYPE => Template::class,
        BulkSms::TYPE => BulkSms::class,
        SenderId::TYPE => SenderId::class
    ];

    protected Connection $connection;

    protected array $attributes = [];
    protected array $fillable = [];
    protected string $endPoint;
    protected string $primaryKey = 'id';
    protected string $namespace = '';
    protected array $singleNestedEntities = [];
    protected array $multipleNestedEntities = [];
    protected bool $isLoaded;

    /**
     * Model constructor.
     * @param Connection $client
     * @param array $attributes
     */
    public function __construct(Connection $client, array $attributes = [])
    {
        $this->connection = $client;
        $this->isLoaded = !method_exists($this, 'findById');
        $this->fill($attributes);
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Fill the entity from array.
     *
     * @param array $attributes
     */
    protected function fill(array $attributes): void
    {
        $attributes = $this->fillableFromArray($attributes);

        foreach ($attributes as $key => $attribute) {
            $this->setAttribute($key, $attribute);
        }

        if (!empty($attributes)) {
            $loadedAttributes = $attributes;
            unset($loadedAttributes[$this->primaryKey]);
            $this->isLoaded = !empty($loadedAttributes);
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    protected function setAttribute(string $key, $value): void
    {
        if ($this->isFillable($key)) {
            $this->attributes[$key] = $this->addReferences($value);
        }
    }

    /**
     * @param $data
     * @return array
     */
    private function addReferences($data): array
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->addReferences($value);
        }

        return $data;
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function fillableFromArray(array $attributes): array
    {
        if (count($this->fillable) > 0) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }

        return $attributes;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isFillable(string $key): bool
    {
        return in_array($key, $this->fillable);
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        if (!$this->isLoaded && method_exists($this, 'findById')) {
            if ($key === $this->primaryKey && isset($this->attributes[$key])) {
                return $this->attributes[$key];
            }

            try {
                $this->findById();
            } catch (SendThenException $apiException) {
                $this->isLoaded = true;
            }
        }

        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }
    /**
     * @return bool
     */
    public function exists(): bool
    {
        if (!array_key_exists($this->primaryKey, $this->attributes)) {
            return false;
        }

        return !empty($this->attributes[$this->primaryKey]);
    }

    /**
     * @return string
     */
    public function json(): string
    {
        $array = $this->getArrayWithNestedObjects();

        return json_encode($array, JSON_FORCE_OBJECT);
    }

    /**
     * @return string
     */
    public function jsonWithNamespace(): string
    {
        if ($this->namespace !== '') {
            return json_encode([$this->namespace => $this->getArrayWithNestedObjects()], JSON_FORCE_OBJECT);
        }

        return $this->json();
    }

    /**
     * @param bool $useAttributesAppend
     * @return array
     */
    private function getArrayWithNestedObjects(bool $useAttributesAppend = true): array
    {
        $result = [];
        $multipleNestedEntities = $this->getMultipleNestedEntities();

        foreach ($this->attributes as $attributeName => $attributeValue) {
            if (!is_object($attributeValue)) {
                $result[$attributeName] = $attributeValue;
            }

            if (array_key_exists($attributeName, $this->getSingleNestedEntities())) {
                $result[$attributeName] = $attributeValue->attributes;
            }

            if (array_key_exists($attributeName, $multipleNestedEntities)) {
                $attributeNameToUse = $attributeName;
                if ($useAttributesAppend) {
                    $attributeNameToUse .= '_attributes';
                }

                $result[$attributeNameToUse] = [];
                foreach ($attributeValue as $attributeEntity) {
                    $result[$attributeNameToUse][] = $attributeEntity->attributes;

                    if ($multipleNestedEntities[$attributeName]['type'] === self::NESTING_TYPE_NESTED_OBJECTS) {
                        $result[$attributeNameToUse] = (object) $result[$attributeNameToUse];
                    }
                }

                if (
                    $multipleNestedEntities[$attributeName]['type'] === self::NESTING_TYPE_NESTED_OBJECTS
                    && empty($result[$attributeNameToUse])
                ) {
                    $result[$attributeNameToUse] = new StdClass();
                }
            }
        }

        return $result;
    }

    /**
     * @param array $response
     * @return $this
     */
    public function makeFromResponse(array $response): self
    {
        $entity = new static($this->connection);
        $entity->selfFromResponse($response);

        return $entity;
    }

    /**
     * @param array $response
     * @return $this
     */
    public function selfFromResponse(array $response): self
    {
        if (isset($response['data'])) {
            $response = $response['data'];
        }

        $this->fill($response);

        foreach ($this->getSingleNestedEntities() as $key => $value) {
            if (isset($response[$key])) {
                $entityName = 'Teamleader\Entities\\' . $value;
                $this->$key = new $entityName($this->connection, $response[$key]);
            }
        }

        foreach ($this->getMultipleNestedEntities() as $key => $value) {
            if (isset($response[$key])) {
                $entityName = 'Teamleader\Entities\\' . $value['entity'];
                $instaniatedEntity = new $entityName($this->connection);
                $this->$key = $instaniatedEntity->collectionFromResult($response[$key]);
            }
        }

        return $this;
    }

    /**
     * @param $result
     * @return array
     */
    public function collectionFromResult($result): array
    {
        if (!$result) {
            return [];
        }

        if (isset($result['data'])) {
            $result = $result['data'];
        }

        // If we have one result which is not an assoc array, make it the first element of an array for the
        // collectionFromResult function so we always return a collection from filter
        if (count(array_filter(array_keys($result), 'is_string'))) {
            $result = [$result];
        }

        $collection = [];
        foreach ($result as $r) {
            $collection[] = static::makeFromResponse($r);
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function getSingleNestedEntities(): array
    {
        return $this->singleNestedEntities;
    }

    /**
     * @return array
     */
    public function getMultipleNestedEntities(): array
    {
        return $this->multipleNestedEntities;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        $result = [];
        foreach ($this->fillable as $attribute) {
            $result[$attribute] = $this->$attribute;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endPoint;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return (isset($this->attributes[$name]) && !is_null($this->attributes[$name]));
    }

    /**
     * @return array|object
     */
    public function jsonSerialize()
    {
        if (!defined('static::TYPE')) {
            return $this->getArrayWithNestedObjects();
        }

        $primaryKey = $this->primaryKey;

        return (object) [
            'type' => static::TYPE,
            $primaryKey => $this->$primaryKey,
        ];
    }

}