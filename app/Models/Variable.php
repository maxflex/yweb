<?php

namespace App\Models;

use Shared\Model;
use App\Models\Service\Parser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variable extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    protected $attributes = [
        'name' => 'новая переменная'
    ];

    protected $fillable = [
        'name',
        'html',
        'desc'
    ];

    public function getHtmlAttribute($value)
    {
        return Parser::compileVars($value);
    }

    public function scopeFindByName($query, $name)
    {
        return $query->where('name', $name);
    }

    public static function display($name, $useful_block = false)
    {
        if (isMobile() && self::findByName($name . '-mobile')->exists()) {
            $name .= '-mobile';
        }
        $html = self::findByName($name)->first()->html;
        if (! $useful_block) {
            Parser::replace($html, 'useful', '');
        }
        return $html;
    }
}
