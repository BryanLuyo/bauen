<?php

namespace App\Http\Controllers\Buy_Sell;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use App;
use Illuminate\Support\Facades\Lang;

class LanguageController extends Controller
{
    
	public function  changeLanguage(Request $request){
        if($request->ajax()){
            $request->session()->put('locale',$request->locale);
            $request->session()->flash('alert-success',('app.Locale_Change_Success'));
        }
    }
    
	public function language_change(Request $data){
        if(!empty($data->input('locale'))){
            $request->session()->put('locale',$data->locale);
            $request->session()->flash('alert-success',('app.Locale_Change_Success'));
        }
	}
}
