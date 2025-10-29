<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{   
    protected $view_path = 'student.inner.subscription';
    public function index(){
         return view($this->view_path . '.pricing');
    }
}
