<?php

namespace System\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use System\Models\Advert;

class IndexController extends Controller
{
    public function index()
    {
        $adverts['home_features'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.web.adverts.home_features'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return view('frontend::index.index', compact('adverts'));
    }
}
