<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function __invoke(Page $page)
    {
        return view('pages.' . $page->template, [
            'page' => $page,
        ]);
    }
}