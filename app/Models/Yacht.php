<?php

namespace App\Models;

use Shared\Model;

class Yacht extends Model
{
    protected $connection = 'ycrm';

    const UPLOAD_DIR = 'storage/img/yachts/';
    const TYPES = ['катер', 'моторная яхта', 'парусная яхта'];
    const BODIES = ['алюминий', 'стеклопластик'];

    protected $commaSeparated = [
        'photos',
    ];

    protected $appends = ['images'];

    public function getImagesAttribute()
    {
        $images = [];
        foreach ($this->photos as $index => $photo) {
            $images[] = [
                'id'  => $index + 1,
                'url' => config('app.crm-url') . self::UPLOAD_DIR . $photo
            ];
        }
        return $images;
    }

    /**
     * Search tutors by params
     */
    public static function search($search)
    {
        @extract($search);

        $query = Yacht::query();

        // if (isset($subject_id) && $subject_id) {
        //     $query->whereSubject($subject_id);
        // }
        //
        // $query->selectDefault()->orderBy('clients_count', 'desc');

        return $query;
    }
}