<?php

namespace hahaha\template\page\demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewType;

class hahaha_controller_multiple extends Controller
{
    public function Index(Request $request): ViewType
    {
        $focus_ = (string) $request->query('focus', 'cache');
        $page_config_ = hahaha_config_multiple::Instance()->Clear()->Initial($focus_);

        return View::file(base_path('template/page/node/multiple/view/hahaha_view_multiple.blade.php'), [
            'page_config_' => $page_config_,
        ]);
    }
}
