<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Yacht extends Model
{
    protected $connection = 'ycrm';
    protected $table = 'api_yachts';
    protected $appends = ['lowest_price'];
    protected $with = ['prices'];
    protected $hidden = ['picturesURL'];

    const URL = 'yacht';

    public function getImagesAttribute()
    {
        $urls = explode(',', $this->attributes['picturesURL']);
        $images = [];
        foreach($urls as $index => $url) {
            $id = $index + 1;
            $images[] = compact('id', 'url');
        }
        return $images;
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'locationId');
    }

    public function getLowestPriceAttribute()
    {
        $lowest_price = $this->prices[0];
        foreach($this->prices as $price) {
            if ($price->price < $lowest_price->price) {
                $lowest_price = $price;
            }
        }
        return $lowest_price;
    }

    /**
     * Search tutors by params
     */
    public static function search($search)
    {
        @extract($search);

        $query = self::query();

        if (isset($beds_from) && $beds_from) {
            $query->where('berthsTotal', '>=', $beds_from);
        }

        if (isset($beds_to) && $beds_to) {
            $query->where('berthsTotal', '<=', $beds_to);
        }

        return $query;
    }
}
