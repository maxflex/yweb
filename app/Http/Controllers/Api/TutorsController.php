<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Page;
use DB;

class TutorsController extends Controller
{
    public function index(Request $request)
    {
        return Tutor::simplePaginate(10);
    }

    /**
     * Получить отзывы
     */
    public function reviews($id)
    {
        return Tutor::reviews($id)->get();
    }

    /**
     * Поиск по преподам
     */
    public function search(Request $request)
    {
        $take = $request->take ?: 10;

        // потому что надо поменять subjects, а из $request нельзя
        $search = $request->search;

        @extract($search);

        // force current page
        Paginator::currentPageResolver(function() use ($request) {
            return $request->page;
        });

        return Tutor::search($search)->paginate($take);
    }
}
