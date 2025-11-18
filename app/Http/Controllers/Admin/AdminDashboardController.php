<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{   
    protected  $view_path = 'admin.';
    public function index(Request $request){
        return view($this->view_path.'dashboard');
    }
}
