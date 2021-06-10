<?php


namespace SendThen\Entities;


use SendThen\Actions\FindAll;
use SendThen\Actions\FindById;
use SendThen\Model;

class Template extends Model
{
    use FindById;
    use FindAll;

    const TYPE = 'template';

    protected string $endPoint = 'templates';

    protected array $fillable = [
        'id',
        'content',
        'createdAt',
        'title',
        'updateAt'
    ];
}