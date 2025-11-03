<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;

class TeacherController extends Controller
{
    public function searchTeachers(Request $request){
        $filters = $request->only(['name', 'start_utc', 'end_utc', 'include_past']);
        $teachers = User::teachers()->with('teacherProfile')->withFilter($filters)->get();
        return view('student.inner.teacher.search',[
            'teachers' => $teachers,
            'filters' => $filters,
        ]);
    }
}
