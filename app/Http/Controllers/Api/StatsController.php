<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Review;
use DB;
use Cache;

class StatsController extends Controller
{
    /**
     * Все отзывы
     *
     */
    public function index(Request $request)
    {
        $query = static::_query($request)->withStudent()->orderBy('score', 'desc');

        $paginator = $query->simplePaginate(50);

        $reviews = $paginator->getCollection()->map(function ($review) {
            $review->tutor = Cache::remember(cacheKey('stats-tutor', $review->id_teacher), 60 * 24, function() use ($review) {
                return DB::connection('egerep')->table('tutors')->whereId($review->id_teacher)->select('id', 'first_name', 'last_name', 'middle_name')->first();
            });
            return $review;
        });

        $return = [
            'reviews'        => $reviews,
            'has_more_pages' => $paginator->hasMorePages(),
        ];

        if ($request->page == 1) {
            $return['total'] = $query->count();

            $avg = $query->where('score', '>', 0)->avg('score');
            $return['avg'] = round($avg, $avg >= 10 ? 1 : 2);
            $return['counts'] = static::_counts($request, true);
        }

        return $return;
    }

    /**
     * $exists – counts не нужны, только exists
     */
    private static function _counts($request, $exists = false)
    {
        $counts = [];

        foreach(array_merge([(object)['id' => '']], Cache::get(cacheKey('review-tutors'))->all()) as $tutor) {
            $new_request = (object)$request->all();
            $new_request->tutor_id = $tutor->id;
            $counts['tutor'][$tutor->id] = static::_query($new_request)->{$exists ? 'exists' : 'count'}();
        }

        // генерируем grade_subjects (stats.coffee:getSubjectsGrades)
        $subject_grades = ['1-11-1', '1-11-0'];
        foreach(dbFactory('subjects')->pluck('id') as $id_subject) {
            foreach([11, 10, 9] as $grade) {
                if ($id_subject == 1 && $grade == 11) {
                    continue;
                }
                $subject_grades[] = $id_subject . '-' . $grade;
            }
        }

        foreach(array_merge([''], $subject_grades) as $subject_grade) {
            $new_request = (object)$request->all();
            $new_request->subject_grade = $subject_grade;
            $counts['subject_grade'][$subject_grade] = static::_query($new_request)->{$exists ? 'exists' : 'count'}();
        }

        return $counts;
    }

    private static function _query($request)
    {
        $query = Review::query();

        if (isset($request->tutor_id) && $request->tutor_id) {
            $query->where('id_teacher', $request->tutor_id);
        }

        if (isset($request->year) && $request->year) {
            $query->where('year', $request->year);
        }

        if (isset($request->subject_grade) && $request->subject_grade) {
            // профильный – max_score = 100
            @list($subject_id, $grade, $profile) = explode('-', $request->subject_grade);

            $query->where('id_subject', $subject_id);
            $query->where('teacher_reviews.grade', $grade);

            if (isset($profile)) {
                $query->where('max_score', $profile ? '=' : '<>', 100);
            }
        }

        return $query;
    }
}
