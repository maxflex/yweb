<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Photo;
use App\Models\Service\Parser;

class PhotoGroup extends Model
{
    public $fillable = ['photo'];

    public function photo()
    {
        return $this->hasMany(Photo::class, 'group_id')->orderBy('position', 'asc');
    }

    public static function getAll($ids = [])
    {
        $groups = self::with(['photo' => function ($query) use ($ids) {
            if (count($ids)) {
                $query->whereIn('id', $ids);
            }

            $query->orderBy('position', 'asc');
        }])->orderBy('position', 'asc')->get();

        $groups->add(new PhotoGroup([
            'photo' => Photo::whereNull('group_id')->whereIn('id', $ids)->orderBy('position', 'asc')->get()
        ]));

        return $groups;
    }
}
