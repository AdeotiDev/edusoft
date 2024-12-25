<x-filament-panels::page>
    @if($resultUploads->isEmpty())
        <p>No results uploaded for this result root.</p>
    @else
        {{-- Group results by class --}}

       <div style="background: #003333; color:#fff; text-align:center; padding:10px 0px;">
        <a href="{{ route('download-report-cards', $record->id) }}" class="btn btn-primary" style="border-radius:10px; border:1px solid #fff; padding:5px 10px;" >Download Report Cards as PDF</a>
       </div> 
       


        @php
            $resultsByClass = [];
            $classNames = [];

            foreach ($resultUploads as $resultUpload) {
                $class = App\Models\SchoolClass::find($resultUpload->class_id);

                
                $className = $class->name ?? 'Unknown Class';

                // Group results by class ID
                $resultsByClass[$resultUpload->class_id][] = $resultUpload;
                $classNames[$resultUpload->class_id] = $className;
            }
        @endphp

        {{-- Tabs for each class --}}
        <div class="tabs">
            <ul class="flex mb-4 border-b">
                @foreach($classNames as $classId => $className)
                    <li class="mr-2">
                        <button 
                            class="tab-toggle px-4 py-2 rounded-t-lg bg-gray-200 hover:bg-gray-300" 
                            data-tab="tab-{{ $classId }}">{{ $className }}</button>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tab Content for each class --}}
        @foreach($resultsByClass as $classId => $classResults)
            <div class="tab-content hidden" id="tab-{{ $classId }}">
                {{-- Class Header --}}
                <h2 class="text-2xl font-semibold mb-4">{{ $classNames[$classId] }}</h2>

                {{-- Collect students and dynamic headers --}}
                @php
                    $students = [];
                    $dynamicHeaders = []; // To track all possible score headers dynamically

                    foreach ($classResults as $resultUpload) {
                        $subject = App\Models\Subject::find($resultUpload->subject_id);
                        $cardItems = is_array($resultUpload->card_items) ? $resultUpload->card_items : json_decode($resultUpload->card_items, true);

                        foreach ($cardItems as $studentId => $result) {
                            $student = App\Models\User::find($studentId);
                            if ($student) {
                                $students[$studentId]['info'] = $student;
                                $students[$studentId]['subjects'][] = [
                                    'name' => $subject->name ?? 'No Subject',
                                    'scores' => $result['scores'] ?? [],
                                    'total' => $result['total'] ?? 'N/A',
                                    'average' => $result['average'] ?? 'N/A',
                                    'highest' => $result['highest'] ?? 'N/A',
                                    'lowest' => $result['lowest'] ?? 'N/A',
                                    'grade' => $result['grade'] ?? 'N/A',
                                    'remark' => $result['remark'] ?? 'N/A',
                                ];

                                // Collect headers dynamically
                                $dynamicHeaders = array_unique(array_merge($dynamicHeaders, array_keys($result['scores'] ?? [])));
                            }
                        }
                    }
                    $school_logo = $schoolDetails['school_logo'];
                    $principal_signature = $schoolDetails['principal_signature'];
                @endphp

                {{-- Render cards for each student --}}
                @foreach ($students as $studentId => $studentData)
                <div style="height:10px; background:#eaf0f8;"></div>
                    <div class="border p-6 mb-6 rounded-lg shadow-lg" style="margin-top:15px; margin-bottom:15px;">
                        <div class="mb-4 flex justify-between border p-2">
                            <div class="school_logo">
                                <img src="{{ Storage::url($school_logo) }}" alt="Logo" class="logo-img" style="height: 70px; border-radius: 10%;">
                            </div>
                          <div class="text-center">  <h2 class="font-bold" style="font-size: 2.7rem;">{{ $schoolDetails['school_name'] }}</h2>
                            <p><b>Address: </b> {{ $schoolDetails['school_address'] }}</p>
                            <p><b>Phone:</b> {{ $schoolDetails['school_phone'] }}</p>
                            </div>
                            <div class="student_passport">
                                <img src="{{ Storage::url($studentData['info']->passport) }}" alt="Logo" class="logo-img" style="height: 70px; border-radius: 10%;">
                            </div>
                        </div>
                        
                        
                        {{-- Student Info --}}
                        

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

                        {{-- Subjects Table --}}
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
                @endforeach
            </div>
        @endforeach
    @endif

    {{-- Tab Switching Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-toggle');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    // Hide all tab contents
                    contents.forEach(content => content.classList.add('hidden'));
                    // Remove active class from tabs
                    tabs.forEach(t => t.classList.remove('bg-gray-300'));

                    // Show the selected tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.remove('hidden');
                    this.classList.add('bg-gray-300');
                });
            });

            // Trigger the first tab by default
            if (tabs.length > 0) {
                tabs[0].click();
            }
        });
    </script>
</x-filament-panels::page>
