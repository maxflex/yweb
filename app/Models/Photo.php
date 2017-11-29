<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    public $timestamps  = false;
    const  STORAGE_DIR  = 'storage/images/';
    const  THUMB_URL    = 'resize/small/';

    public $appends = [
        'url',
        'thumb_url'
    ];

    public function getUrlAttribute()
    {
        return config('app.crm-url') . self::STORAGE_DIR . $this->filename;
    }

    public function getThumbUrlAttribute()
    {
        return config('app.crm-url') . static::THUMB_URL . $this->filename;
    }

    public static function parse($ids, $hide_link = false)
    {
        return view('gallery.index')->with([
            'hide_link' => $hide_link,
            'urls' => PhotoGroup::getAll($ids)
        ]);
    }
}
