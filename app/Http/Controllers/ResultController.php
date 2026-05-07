<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\ResultUpload;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function downloadResult($recordId)
    {

        $resultUploads = ResultUpload::where('result_root_id', $recordId)->get();
        $schoolDetails = getSchoolDetails();

        $loggedInStudentId = Auth::user()->id;

        $studentData = [];
        $dynamicHeaders = [];

        foreach ($resultUploads as $resultUpload) {
            $subject = Subject::find($resultUpload->subject_id);
            $class = SchoolClass::find($resultUpload->class_id);
            $cardItems = is_array($resultUpload->card_items) ? $resultUpload->card_items : json_decode($resultUpload->card_items, true);

            if (isset($cardItems[$loggedInStudentId])) {
                $result = $cardItems[$loggedInStudentId];
                $student = User::find($loggedInStudentId);

                if ($student) {
                    $studentData['info'] = $student;
                    $studentData['subjects'][] = [
                        'name' => $subject->name ?? 'No Subject',
                        'scores' => $result['scores'] ?? [],
                        'total' => $result['total'] ?? 'N/A',
                        'average' => $result['average'] ?? 'N/A',
                        'highest' => $result['highest'] ?? 'N/A',
                        'lowest' => $result['lowest'] ?? 'N/A',
                        'grade' => $result['grade'] ?? 'N/A',
                        'remark' => $result['remark'] ?? 'N/A',
                    ];

                    $dynamicHeaders = array_unique(array_merge($dynamicHeaders, array_keys($result['scores'] ?? [])));
                }
            }
        }

        if (empty($studentData['subjects'])) {
            return back()->with('error', 'No results available to download.');
        }

        // Pass data to the PDF view
        $pdf = Pdf::loadView('pdf.result', compact('studentData', 'dynamicHeaders', 'schoolDetails', 'recordId'))
            ->setPaper('a4', 'portrait');

        // Return PDF for download
        return $pdf->download('student_result.pdf');
    }
}
