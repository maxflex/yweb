<?php

namespace App\Models;

use Shared\Model;
use App\Models\Variable;
use App\Models\Service\Parser;
use App\Scopes\PageScope;
use App\Models\Service\Factory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    // Соответствия между разделами и ID предмета
    static $subject_page_id = [
        '1'   => 194,
        '2'   => 195,
        '3'   => 198,
        '4'   => 199,
        '5'   => 203,
        '6'   => 196,
        '7'   => 197,
        '8'   => 201,
        '9'   => 200,
        '10'  => 202,
        '1,2' => 247,
    ];

    // also serp fields
    protected $casts = [
       'id'         => 'int', // ID нужен, чтобы идентифицировать текущую страницу в search
       'place'      => 'string',
       'station_id' => 'string',
       'subjects'   => 'string',
       'sort'       => 'string',
   ];

    public function useful()
    {
        return $this->hasMany(PageUseful::class);
    }

    public function getSubjectsAttribute($value)
    {
        if ($value) {
            $subjects = explode(',', $value);
            foreach($subjects as $subject_id) {
                $return[$subject_id] = true;
            }
            return (object)$return;
        } else {
            return emptyObject();
        }
    }

    public function getSearchAttribute()
    {
        foreach($this->casts as $field => $type) {
            $data[$field] = $this->{$field};
        }
        if ($this->hidden_filter) {
            $data['hidden_filter'] = explode(',', str_replace(' ', '', mb_strtolower($this->hidden_filter)));
        }
        return json_encode($data, JSON_FORCE_OBJECT);
    }

    public function getHtmlAttribute($value)
    {
        $value = isMobile() ? $this->attributes['html_mobile'] : $this->attributes['html'];
        $value = Parser::compileVars($value);
        return Parser::compilePage($this, $value);
    }

    public function getH1Attribute($value)
    {
        if ($value) {
            return "<div class='h1-top'>{$value}</div>";
        }
        return ' ';
    }

    public function getH1BottomAttribute($value)
    {
        if ($value) {
            return "<h1 class='h1-bottom'>{$value}</h1>";
        }
        return ' ';
    }

    public function scopeWhereSubject($query, $subject_id)
    {
        return $query->whereRaw("FIND_IN_SET($subject_id, subjects)");;
    }

    public function scopeFindByParams($query, $search)
    {
        @extract($search);

        $query->where('subjects', implode(',', $subjects));
        $query->where('place', setOrNull(@$place));
        $query->where('sort', setOrNull(@$sort));
        $query->where('station_id', setOrNull(@$station_id));
        $query->where('id', '!=', $id);

        return $query;
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PageScope);
    }

    public static function getUrl($id)
    {
        return self::whereId($id)->value('url');
    }

    public static function getSubjectUrl($subject_eng)
    {
        return self::getUrl(Page::$subject_page_id[Factory::getSubjectId($subject_eng)]);
    }

    public static function getSubjectRoutes()
    {
        $subject_routes = [];
        foreach(self::$subject_page_id as $subject_id => $page_id) {
            // ссылки только к отдельным предметам
            if (strpos($subject_id, ',') === false) {
                $subject_routes[$subject_id] = self::getUrl($page_id);
            }
        }
        return $subject_routes;
    }

    /**
     * Главная страница серпа
     */
    public function isMainSerp()
    {
        return $this->id == 10;
    }
}
