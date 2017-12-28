<?php

namespace App\Models;

use Shared\Model;

class Yacht extends Model
{
    protected $connection = 'ycrm';

    const UPLOAD_DIR = 'storage/img/yachts/';
    const TYPES = ['катер', 'моторная яхта', 'парусная яхта', 'яхта с флайбриджем'];
    const GAS_TYPES = ['дизель', 'бензин'];
    const BODIES = ['алюминий', 'стеклопластик'];

    protected $commaSeparated = [
        'photos',
    ];

    protected $appends = ['images', 'type_string', 'gas_type_string', 'body_string'];

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

    public function getBodyStringAttribute()
    {
        return self::BODIES[$this->body];
    }

    public function getTypeStringAttribute()
    {
        return self::TYPES[$this->type];
    }

    public function getGasTypeStringAttribute()
    {
        return self::GAS_TYPES[$this->gas_type];
    }

    /**
     * Search tutors by params
     */
    public static function search($search)
    {
        @extract($search);

        $query = Yacht::query();

        if (isset($manufacturer) && $manufacturer) {
            $query->where('manufacturer', $manufacturer);
        }
        //
        // $query->selectDefault()->orderBy('clients_count', 'desc');

        return $query;
    }
}