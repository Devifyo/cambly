<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\User;

class TeacherController extends Controller
{

public function searchTeachers(Request $request)
{
    $request->validate([
        'name' => 'nullable|string|max:255',
        'start_utc' => 'nullable|date_format:Y-m-d H:i',
        'gender' => 'nullable|in:male,female',
        'languages' => 'nullable|array',
        'languages.*' => 'string',
    ]);

    $filters = $request->only(['name', 'start_utc', 'gender', 'languages']);

    $teachers = User::teachers()
        ->filterByName($request->name)
        ->filterByGender($request->gender)
        ->filterByLanguage($request->languages)
        ->filterByAvailability($request->start_utc)
        ->withTeacherData($request->start_utc)
        ->paginate(5)
        ->appends($filters);
        
    return view('student.inner.teacher.search', compact('teachers', 'filters'));
}




}
