<?php

namespace App\Models;

use App\Scopes\TutorScope;
use DB;
use App\Service\Cacher;

class Tutor extends Service\Model
{
    protected $connection = 'egerep';

    static $phone_fields = ['phone', 'phone2', 'phone3', 'phone4'];

    protected $appends = [
        'subjects_string',
        'subjects_string_common',
        'types' // ЕГЭ/ОГЭ
    ];

    const USER_TYPE  = 'TEACHER';

    const URL = 'tutors';

    const SHORT_LIST_COUNT = 3;

    protected $commaSeparated = ['subjects', 'grades', 'branches'];

    protected $multiLine = ['public_desc', 'education', 'achievements', 'experience', 'preferences'];

    public function departure()
    {
        return $this->hasMany(TutorDeparture::class);
    }

    public function markers()
    {
        return $this->morphMany(Marker::class, 'markerable')->where('type', 'green');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class)->latest()->take(3);
    }

    public function plannedAccount()
    {
        return $this->hasOne(PlannedAccount::class);
    }

    public function getSubjectsStringAttribute()
    {
        return implode(', ', array_map(function($subject_id) {
            return Cacher::getSubjectName($subject_id, 'dative');
        }, $this->subjects));
    }

    public function getSubjectsStringCommonAttribute()
    {
        $subjects = [];
        foreach($this->subjects as $subject_id) {
            $name = Cacher::getSubjectName($subject_id, 'name');
            if (count($this->types)) {
                $name .= " ({$this->types})";
            }
            $subjects[] = $name;
        }
        return implode(', ', $subjects);
    }

    public function getTypesAttribute()
    {
        $types = [];
        if (in_array(11, $this->grades)) {
            $types[] = 'ЕГЭ';
        }
        if (in_array(9, $this->grades)) {
            $types[] = 'ОГЭ';
        }
        return implode(', ', $types);
    }

    public static function boot()
    {
        static::addGlobalScope(new TutorScope);
    }

    public function scopeWhereSubject($query, $subject_id)
    {
        return $query->whereRaw("FIND_IN_SET($subject_id, subjects)");
    }

    /**
     * Search tutors by params
     */
    public static function search($search)
    {
        @extract($search);

        $query = Tutor::query();

        if (isset($subject_id) && $subject_id) {
            $query->whereSubject($subject_id);
        }

        $query->selectDefault()->orderBy('clients_count', 'desc');

        return $query;
    }

    /**
     * @todo: проанализировать где какие поля используются и вынести в Global Scope
     */
    public function scopeSelectDefault($query)
    {
        return $query->select([
            'tutors.id',
            'first_name',
            'middle_name',
            'last_name',
            'subjects',
            'public_desc',
            'photo_extension',
            'start_career_year',
            'birth_year',
            'lesson_duration',
            'public_price',
            'departure_price',
            'education',
            'achievements',
            'preferences',
            'experience',
            'tutoring_experience',
            'grades',
            'gender',
            'lk',
            'tb',
            'js',
            'video_link',
            'video_duration',
            'description',
            'tutor_data.clients_count',
            'tutor_data.reviews_count_egecrm as reviews_count',
            'tutor_data.review_avg',
            'tutor_data.svg_map',
            'tutor_data.photo_exists',
        ])->join('tutor_data', 'tutor_data.tutor_id', '=', 'tutors.id');
    }

    public function scopeLight($query)
    {
        return $query->select([
            'tutors.id',
            'first_name',
            'middle_name',
            'last_name',
            'subjects',
            'photo_extension',
            'start_career_year',
            'grades',
            'tutor_data.photo_exists',
        ])->join('tutor_data', 'tutor_data.tutor_id', '=', 'tutors.id');
    }

    public static function bySubject($subject_eng, $limit = false, $grade = false)
    {
        $query = static::query();
        if ($subject_eng != 'any') {
            $subject_id = Service\Factory::getSubjectId($subject_eng);
            $query->whereSubject($subject_id);
        } else {
            $query->inRandomOrder();
        }

        if ($grade) {
            $query->whereRaw("FIND_IN_SET($grade, grades)");
        }

        return $query->light()
                     ->take($limit ?: static::SHORT_LIST_COUNT)
                     ->get();
    }
}
