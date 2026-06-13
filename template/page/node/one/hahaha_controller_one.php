<?php

namespace hahaha\template\page\demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewType;

class hahaha_controller_one extends Controller
{
    public function Index(Request $request): ViewType
    {
        $type_ = (string) $request->query('type', 'featured');
        $page_config_ = hahaha_config_one::Instance()->Clear()->Initial($type_);

        return View::file(base_path('template/page/node/one/hahaha_view_one.blade.php'), [
            'page_config_' => $page_config_,
        ]);
    }
}
