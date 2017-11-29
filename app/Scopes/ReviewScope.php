<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use DB;

class ReviewScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('published', 1)
                // ->orderBy('teacher_reviews.id', 'desc')
                ->addSelect(DB::raw('teacher_reviews.id, admin_comment_final as comment, IF(admin_rating_final=6, 0, admin_rating_final) as rating,
                    score, max_score, signature, id_subject, id_student, id_teacher, year, teacher_reviews.grade, teacher_reviews.date'));
    }
}
