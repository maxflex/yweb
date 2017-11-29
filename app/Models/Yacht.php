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

    protected $appends = ['photo_urls'];

    public function getPhotoUrlsAttribute()
    {
        $urls = [];
        foreach ($this->photos as $photo) {
            $urls[] = config('app.crm-url') . self::UPLOAD_DIR . $photo;
        }
        return $urls;
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