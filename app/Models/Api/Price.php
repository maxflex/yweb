<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'api_prices';
    protected $connection = 'ycrm';
}
