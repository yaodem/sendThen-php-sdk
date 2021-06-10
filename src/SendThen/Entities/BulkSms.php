<?php


namespace SendThen\Entities;


use SendThen\Actions\FindAll;
use SendThen\Actions\FindById;
use SendThen\Actions\Storable;
use SendThen\Model;
use SendThenException;

class BulkSms extends Model
{
    use FindById;
    use FindAll;
    use Storable;

    const TYPE = 'campaign';

    protected string $endPoint = 'campaigns';

    protected array $fillable = [
        'id',
        'body',
        'createdAt',
        'errorCode',
        'errorDescription',
        'isClear',
        'isFlash',
        'isUnicode',
        'recipients',
        'scheduledDatetime',
        'senderId',
        'updatedAt'
    ];

    /**
     * @param array|null $data
     * @param string|null $body
     * @param bool $isFlash
     * @param bool|null $isUnicode
     * @param string|null $scheduledDatetime
     * @param array|null $recipients
     * @param string|null $senderId
     * @return mixed
     * @throws SendThenException
     */
    public function send(
        ?array $data,
        ?string $body,
        ?bool $isFlash,
        ?bool $isUnicode,
        ?string $scheduledDatetime,
        ?array $recipients,
        ?string $senderId
    )
    {
        $arguments = [
            'body' => $data['body'] ?? $body,
            'isFlash' => $data['isFlash'] ?? $isFlash,
            'isUnicode' => $data['isUnicode'] ?? $isUnicode,
            'scheduledDatetime' => $data['scheduledDatetime'] ?? $scheduledDatetime,
            'recipients' => $data['recipients'] ?? $recipients,
            'senderId' => $data['senderId'] ?? $senderId
        ];

        return $this->connection()->post($this->getEndpoint() . '.newCampaign', json_encode($arguments, JSON_FORCE_OBJECT));
    }
}