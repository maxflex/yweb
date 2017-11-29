<?php

namespace App\Models;

use App\Scopes\ReviewScope;
use Illuminate\Database\Eloquent\Model;
use App\Service\Cacher;
use App\Service\Months;
use Cache;
use DB;

class Review extends Model
{
    protected $connection = 'egecrm';
    protected $table = 'teacher_reviews';
    protected $appends = ['subject_string', 'date_string'];

    public function getSubjectStringAttribute()
    {
        $id_subject = $this->attributes['id_subject'];
        return Cache::remember(cacheKey('subject-dative', $id_subject), 60 * 24, function() use ($id_subject) {
            return Cacher::getSubjectName($id_subject, 'dative');
        });
    }

    public function getDateStringAttribute()
    {
        $date = $this->attributes['date'];
        return date('j ', strtotime($date)) . Months::SHORT[date('n', strtotime($date))] . date(' Y', strtotime($date));
    }

    public function scopeWithStudent($query)
    {
        return $query->join('students', 'students.id', '=', 'teacher_reviews.id_student')
            ->join('users', function($join) {
                $join->on('users.id_entity', '=', 'students.id')->on('users.type', '=', \DB::raw("'STUDENT'"));
            })
            ->addSelect('students.first_name as student_first_name',
                        'students.last_name as student_last_name',
                        'students.middle_name as student_middle_name',
                        'users.photo_extension');
    }

    public static function boot()
    {
        static::addGlobalScope(new ReviewScope);
    }
}
