<?php


namespace SendThen\Entities;


use SendThen\Actions\FindAll;
use SendThen\Actions\FindById;
use SendThen\Model;

class SenderId extends Model
{
    use FindById;
    use FindAll;

    const TYPE = 'senderId';

    protected string $endPoint = 'senderIds';

    protected array $fillable = [
        'id',
        'approved',
        'createdAt',
        'isDefault',
        'purpose',
        'state',
        'title',
        'updatedAt'
    ];
}