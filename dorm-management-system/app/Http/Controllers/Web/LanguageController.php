<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    function languageSwitch(Request $request){
        $language = $request->input('language');

        session(['language' => $language]);
//        dd(session('language'));

        return redirect()->back()->with (['language_switched' => $language]);

    }
}
