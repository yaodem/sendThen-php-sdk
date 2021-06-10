<?php


namespace SendThen\Entities;


use SendThen\Actions\FindAll;
use SendThen\Actions\FindById;
use SendThen\Model;

class Group extends Model
{
    use FindById;
    use FindAll;

    const TYPE = 'group';

    protected string $endPoint = 'groups';

    protected array $fillable = [
        'id',
        'contacts',
        'createdAt',
        'groupName',
        'updatedAt'
    ];
}