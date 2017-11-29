<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page;
use App\Models\Variable;
use Storage;

/**
 * Перед запуском модифицировать helpers.php – getSize
 */
class FindAllLinksNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:links-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $links = [];

        foreach(Page::take(1)->get() as $page) {
            $links = array_merge($links, self::findHrefs($page->html, $page->url));
            // $links = array_merge($links, self::findHrefs($page->html_mobile, $page->url));
        }

        $links = array_unique($links);
        sort($links);
        dd($links);
        Storage::put('links_new.txt', implode("\n", $links));

        $custom_check = ["", "================", "CUSTOM CHECK", "================"];
        $error_links  = ["================", "ERROR LINKS", "================"];

        foreach($links as $link) {
            if ($link[0] == '/' && strpos($link, 'http') === false) {
                $status_code = self::getStatusCode($link);
                dump($link . " | " . $status_code);
                if ($status_code != 200) {
                    $this->error("error");
                    $error_links[] = $link;
                } else {
                    $this->info("ok");
                }
            } else {
                $this->line("custom: $link");
                $custom_check[] = $link;
            }
        }

        Storage::put('link_problems_new.txt', implode("\n", array_merge($error_links, $custom_check)));
    }

    /**
     * $prefix – если ссылка не от корня, учитываем URL страницы
     */
    private static function findHrefs($html, $prefix = null)
    {
        preg_match_all('/href=\'(.+)\'/U', $html, $m1);
        preg_match_all('/href="(.+)"/U', $html, $m2);
        $links = array_merge($m1[1], $m2[1]);
        if ($prefix) {
            if ($prefix[strlen($prefix) - 1] != '/') {
                $prefix = $prefix . '/';
            }
            if ($prefix[0] != '/') {
                $prefix = '/' . $prefix;
            }
            foreach($links as &$link) {
                if ($link[0] != '/' && strpos($link, 'http') === false) {
                    $link = $prefix . $link;
                }
            }
        }

        return $links;
    }

    private static function getStatusCode($url)
    {
        $handle = curl_init('https://ege-centr.ru' . $url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        return $httpCode;
    }
}
