<x-filament-panels::page>
    <div> <!-- Root tag to fix Livewire's requirement -->
        @if($resultUploads->isEmpty())
            <p>No results uploaded for this result root.</p>
        @else
            <div>
                <button 
                    onclick="printResult()" 
                    style="background-color: #4CAF50; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    Print result
                </button>


            </div>
            <div id="printableArea">
            @php
                $loggedInStudentId = auth()->user()->id;
                $school_logo = $schoolDetails['school_logo'];
                $principal_signature = $schoolDetails['principal_signature'];
                $studentData = [];
                $dynamicHeaders = [];

                // Fetch logged-in student's data and subjects
                foreach ($resultUploads as $resultUpload) {
                    $subject = App\Models\Subject::find($resultUpload->subject_id);
                    $class = App\Models\SchoolClass::find($resultUpload->class_id);
                    $cardItems = is_array($resultUpload->card_items) ? $resultUpload->card_items : json_decode($resultUpload->card_items, true);

                    if (isset($cardItems[$loggedInStudentId])) {
                        $result = $cardItems[$loggedInStudentId];
                        $student = App\Models\User::find($loggedInStudentId);

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
            @endphp

            @if(empty($studentData['subjects']))
                <p>No results available for the logged-in student.</p>
            @else
                <div style="height:10px; background:#eaf0f8;"></div>
                <div class="border p-6 mb-6 rounded-lg shadow-lg" style="margin-top:15px; margin-bottom:15px;">
                    <!-- School Header -->
                    <div class="mb-4 flex justify-between border p-2">
                        <div class="school_logo">
                            <img src="{{ Storage::url($school_logo) }}" alt="Logo" class="logo-img" style="height: 70px; border-radius: 10%;">
                        </div>
                        <div class="text-center">
                            <h2 class="font-bold" style="font-size: 2.7rem;">{{ $schoolDetails['school_name'] }}</h2>
                            <p><b>Address: </b> {{ $schoolDetails['school_address'] }}</p>
                            <p><b>Phone:</b> {{ $schoolDetails['school_phone'] }}</p>
                        </div>
                        <div class="student_passport">
                            <img src="{{ Storage::url($studentData['info']->passport) }}" alt="Passport" class="passport-img" style="height: 70px; border-radius: 10%;">
                        </div>
                    </div>

                    <!-- Student Information -->
                    <div class="mb-4 flex justify-between border p-2">
                   
                        <div>
                         <h2 class="text-xl font-bold">{{ $studentData['info']->name }}</h2>
                         <p>Student ID: {{ $studentData['info']->id }}</p>
                         <p>Email: {{ $studentData['info']->email }}</p>
                        </div>
     
                        @php
                         //    $student = App\Models\User::find($studentData['info']->id);
                            // $number_in_class = App\Models\User::whereHas('student')->where('student_class', $student->student_class)->count();

                            if ($student && $student->student_class) {
                            $number_in_class = App\Models\User::whereHas('student', function ($query) use ($student) {
                                $query->where('student_class', $student->student_class);
                            })->count();
                        } else {
                            $number_in_class = ""; // Default value if $student or $student->student_class is null
                        }

                        
                        @endphp
     
                           <!-- Student Details Column -->
                 <div class="details-column">
                    <p class="detail-item"><span class="bold" style="font-weight: 600; color:darkmagenta;">{{$record->name}}</span></p>
                     <p class="detail-item"><span class="bold">Roll Number:</span> {{ $student->student->roll_number ?? 'N/A' }}</p>
                     <p class="detail-item"><span class="bold">Guardian:</span> {{ $student->student->guardian_name ?? 'N/A' }}</p>
                 </div>
     
                 <!-- Student Contact Column -->
                 <div class="contact-column">
                     
                     
                     <p class="contact-item"><span class="bold">Class:</span> {{ $class->name ?? 'N/A' }}</p>
                     <p class="contact-item"><span class="bold">Number In Class:</span> {{ $number_in_class ?? 'N/A' }}</p>
                     <p class="contact-item">
                        <span class="bold">Next Term Begins:</span> 
                        {{ $record->next_term ? \Carbon\Carbon::parse($record->next_term)->format('M j, Y') : 'N/A' }}
                    </p>
                 </div>
                         
                     
                     
                     </div>

                    <!-- Results Table -->
                    <table class="w-full border-collapse border border-gray-300 text-left">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-2 py-1">SUBJECT</th>
                                @foreach ($dynamicHeaders as $header)
                                    <th class="border px-2 py-1">{{ $header }}</th>
                                @endforeach
                                <th class="border px-2 py-1">TOTAL</th>
                                <th class="border px-2 py-1">AVERAGE</th>
                                <th class="border px-2 py-1">HIGHEST</th>
                                <th class="border px-2 py-1">LOWEST</th>
                                <th class="border px-2 py-1">GRADE</th>
                                <th class="border px-2 py-1">REMARK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentData['subjects'] as $subject)
                                <tr>
                                    <td class="border px-2 py-1">{{ $subject['name'] }}</td>
                                    @foreach ($dynamicHeaders as $header)
                                        <td class="border px-2 py-1">{{ $subject['scores'][$header] ?? 'N/A' }}</td>
                                    @endforeach
                                    <td class="border px-2 py-1">{{ $subject['total'] }}</td>
                                    <td class="border px-2 py-1">{{ $subject['average'] }}</td>
                                    <td class="border px-2 py-1">{{ $subject['highest'] }}</td>
                                    <td class="border px-2 py-1">{{ $subject['lowest'] }}</td>
                                    <td class="border px-2 py-1">{{ $subject['grade'] }}</td>
                                    <td class="border px-2 py-1">{{ $subject['remark'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="teacher_comment">
                         {{-- Generate the average of all total divided by the number of subjects --}}
                            <br><hr><br>
                              <table class="w-full border-collapse border border-gray-300 text-left" cellpadding="10">
                                {{-- <tr style="padding:10px;">
                                    <th><b>Overall Total</b></th>
                                    <th><b>Average</b></th>
                                    <th><b>Teacher's Comment</b></th>
                                </tr> --}}
                                <tr>   
                                    <td>{{ array_sum(array_column($studentData['subjects'], 'total')) }}</td>    
                                    <td>{{ round(array_sum(array_column($studentData['subjects'], 'total')) / count($studentData['subjects']), 2) }}</td>    
                                    {{-- Generate random teacher's comment based on the overall average. For example, if $overallAverage >= 90, it can have 'Excellent result!' or 'Outstanding result!' and so on      --}}
                                    <td>
                                        @php
                                            $overallAverage = round(array_sum(array_column($studentData['subjects'], 'total')) / count($studentData['subjects']), 2);
                                            if ($overallAverage >= 90) {
                                                //Let $comment have random value like 'Excellent result!', or 'Outstanding result!', or 'Super performance!'
                                                $comments = ['Excellent result!', 'Outstanding result!', 'Super performance!'];
                                                $comment = $comments[array_rand($comments)];
                                            } elseif ($overallAverage >= 80) {
                                                $comment = 'Very good result!';
                                                $comments = ['Very good result!', 'Keep it up!', 'Keep up your brilliance!'];
                                                $comment = $comments[array_rand($comments)];
                                            } elseif ($overallAverage >= 70) {
                                                $comments = ['Good result!', 'Keep it up!', 'Try harder next time!'];
                                                $comment = $comments[array_rand($comments)];
                                            } elseif ($overallAverage >= 60) {
                                                $comments = ['Fair result!', 'You can do more next time!', 'Keep it up!'];
                                                $comment = $comments[array_rand($comments)];
                                            } else {
                                                $comments = ['Needs improvement!', 'Try harder next time!', 'Stay focused in your subjects!'];
                                                $comment = $comments[array_rand($comments)];
                                            }
                                        @endphp
                                        {{ $comment }}
                                    </td>
                                    <td>
                                        {{ $schoolDetails['principal_name'] }}
                                        <br>
                                        <img src="{{ Storage::url($principal_signature) }}" alt="signature" class="logo-img" style="height: 50px;">

                                    </td>
                                </tr>  
                                <tr style="padding:10px;">
                                    <td><b>Overall Total</b></td>
                                    <td><b>Average</b></td>
                                    <td><b>Teacher's Comment</b></td>
                                    <td><b>Principal</b></td>
                                </tr>


                              </table>  

                              <br><hr><br>

                        </div>
                </div>
            @endif
        @endif

            </div>
    </div>




    <script>
        function printResult() {
            const printContent = document.getElementById('printableArea').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload(); // Refresh to restore JS functionality
        }
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printableArea, #printableArea * {
                visibility: visible;
            }
            #printableArea {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
            button {
                display: none;
            }
        }
    </style>
</x-filament-panels::page>
