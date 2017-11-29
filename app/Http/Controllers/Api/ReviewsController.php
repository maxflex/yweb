<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Review;
use DB;
use Cache;
use App\Service\Cacher;
use App\Service\Months;
use App\Models\Service\Factory;

class ReviewsController extends Controller
{
    /**
     * Все отзывы
     *
     */
    public function index(Request $request)
    {
        $paginator = Review::withStudent()->orderBy('teacher_reviews.admin_rating_final', 'desc')->orderBy('teacher_reviews.date', 'desc')->simplePaginate(20);

        return [
            'reviews'        => $paginator->getCollection(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Все отзывы (блоки)
     *
     */
    public function block(Request $request)
    {
        $query = Review::withStudent()->where('users.photo_extension', '<>', '');

        if ($request->ids) {
            $query->whereNotIn('teacher_reviews.id', $request->ids);
        }

        if ($request->min_score) {
            @list($min_score_ege, $min_score_oge) = explode(',', $request->min_score);
            if ($min_score_oge) {
                $query->whereRaw("((teacher_reviews.score >= {$min_score_ege} AND teacher_reviews.grade=11) OR (teacher_reviews.score >= {$min_score_oge} AND teacher_reviews.grade=9))");
            } else {
                $query->where('teacher_reviews.score', '>=', $request->min_score);
            }
        }

        if ($request->grade) {
            $query->where('teacher_reviews.grade', '=', $request->grade);
        }

        if ($request->subject) {
            $subject_id = Factory::getSubjectId($request->subject);
            $query->where('teacher_reviews.id_subject', '=', $subject_id);
        }

        if ($request->university) {
            $query->orderByRaw("case
                when LOWER(teacher_reviews.admin_comment_final) RLIKE '[[:<:]]" . mb_strtolower($request->university) . "[[:>:]]' then 1
                else 0 end desc
            ");
        }

        $paginator = $query->orderBy('admin_rating_final', 'desc')->inRandomOrder()->simplePaginate($request->count);

        return [
            'reviews' => $paginator->getCollection(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Показать отзывы препода
     *
     */
    public function show($id)
    {
        return Review::withStudent()->where('id_teacher', $id)->orderBy('teacher_reviews.admin_rating_final', 'desc')->orderBy('teacher_reviews.date', 'desc')->get();
    }

}
