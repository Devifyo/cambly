<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function searchTeachers(Request $request){
        return view('student.inner.teacher.search');
    }
}
