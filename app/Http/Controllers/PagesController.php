<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Program;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Page;
use App\Models\Variable;
use App\Models\Tutor;
use App\Models\Service\Parser;

class PagesController extends Controller
{
    /**
     * Все страницы (на самом деле это теперь только серп)
     */
    public function index($url)
    {
        $page = Page::whereUrl($url);
        if (! $page->exists()) {
            $html = Page::withoutGlobalScopes()->whereUrl('404')->first()->html;
            $status = 404;
        } else {
            $html = $page->first()->html;
            $status = 200;
        }
        return response()->view('pages.index', compact('html'), $status);
    }

    /**
     * Tutor profile page
     */
    public function tutor($id)
    {
        if (Tutor::whereId($id)->exists()) {
            $html = Page::whereUrl(Tutor::URL . '/:id')->first()->html;
            Parser::compileTutor($id, $html);
            $status = 200;
        } else {
            $html = Page::withoutGlobalScopes()->whereUrl('404')->first()->html;
            $status = 404;
        }
        $_SESSION['action'] = 'profile';
        return response()->view('pages.index', compact('html'), $status);
    }

    public function about()
    {
        $html = Page::whereUrl(Faq::URL)->first()->html;
        return view('pages.index')->with(compact('html'));
    }
}
