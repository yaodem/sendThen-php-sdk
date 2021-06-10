<?php


namespace SendThen\Entities;

use SendThen\Actions\FindById;
use SendThen\Model;

class Balance extends Model
{
    use FindById;

    const TYPE = 'balance';

    protected string $endPoint = 'balance';

    protected array $fillable = [
        'balance'
    ];
}