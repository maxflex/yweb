<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VarsParseTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        \DB::table('variables')->insertGetId([
            'name' => 'z',
            'html' => 'THIS IS Z!'
        ]);
        $var_id = \DB::table('variables')->insertGetId([
            'name' => 'x',
            'html' => 'Y=[y] A=[a]'
        ]);
        $page_id = \DB::table('pages')->insertGetId([
            'url' => uniqid(),
            'title' => uniqid(),
            'html' => 'testy [x|a=[z]|y=10]',
            'published' => 1,
        ]);
        $this->var = \App\Models\Variable::find($var_id);
        $this->page = \App\Models\Page::find($page_id);
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        dump($this->page->html);
        // $this->assertEquals('testy 10', $this->page->html);
    }
}
