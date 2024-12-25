<?php

use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;

if (!function_exists('getSchoolDetails')) {
    function getSchoolDetails()
    {
        $school = Setting::firstOrFail();

        return [
            'school_name' => $school->school_name,
            'school_address' => $school->address,
            'school_phone' => $school->contact,
            'school_logo' => $school->logo,
            'school_favicon' => $school->favicon,
            'meta_description' => $school->meta_description,
            'meta_title' => $school->meta_title,
            'meta_keywords' => $school->meta_keywords,
            'principal_name' => $school->principal_name,
            'principal_signature' => $school->principal_signature,
        ];
    }




    if (!function_exists('getSchoolStats')) {

        function getSchoolStats()
        {

            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $totalClasses =  SchoolClass::count();


            return [
                'totalStudents' => number_format($totalStudents),
                'totalTeachers' => number_format($totalTeachers),
                'totalClasses' => number_format($totalClasses)
            ];
        }
    }
}
