<?php

namespace App\Models;

use Shared\Model;

class Yacht extends Model
{
    protected $connection = 'ycrm';

    const URL = 'yacht';
    const UPLOAD_DIR = 'storage/img/yachts/';
    const TYPES = ['катер', 'моторная яхта', 'парусная яхта', 'яхта с флайбриджем'];
    const GAS_TYPES = ['дизель', 'бензин'];
    const BODIES = ['алюминий', 'стеклопластик'];

    protected $commaSeparated = [
        'photos',
    ];

    protected $appends = ['images', 'type_string', 'gas_type_string', 'body_string', 'mainPictureUrl'];

    public function getMainPictureUrlAttribute()
    {
        return config('app.crm-url') . self::UPLOAD_DIR . $this->photos[0];
    }

    public function getImagesAttribute()
    {
        $images = [];
        if ($this->photos) {
            foreach ($this->photos as $index => $photo) {
                $images[] = [
                    'id'  => $index + 1,
                    'url' => config('app.crm-url') . self::UPLOAD_DIR . $photo
                ];
            }
        }
        return $images;
    }

    public function getBodyStringAttribute()
    {
        // return self::BODIES[$this->body];
    }

    public function getTypeStringAttribute()
    {
        // return self::TYPES[$this->type];
    }

    public function getGasTypeStringAttribute()
    {
        // return self::GAS_TYPES[$this->gas_type];
    }

    public function getDescriptionAttribute($value)
    {
        return preg_replace("/ *[\r\n]+/", "<br />", $value);
    }

    public function getPricesAttribute($value)
    {
        return preg_replace("/ *[\r\n]+/", "<br />", $value);
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

        if (isset($price_from) && $price_from) {
            $query->where('price', '>=', $price_from);
        }

        if (isset($price_to) && $price_to) {
            $query->where('price', '<=', $price_to);
        }

        if (isset($length_from) && $length_from) {
            $query->where('length', '>=', $length_from);
        }

        if (isset($length_to) && $length_to) {
            $query->where('length', '<=', $length_to);
        }

        // $query->selectDefault()->orderBy('clients_count', 'desc');

        return $query;
    }
}