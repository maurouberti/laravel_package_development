<?php

namespace Modules\Pages\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Pages\Page;
use Location;

class PagesController extends Controller {
    public function index() {
        $Pages = Page::all();
        return view('Page::index', ['Pages' => $Pages]);
    }
    
    public function view() {
        echo config('pages.home');
        echo config('pages.error_page');
        echo Location::getLocale();
    }
}