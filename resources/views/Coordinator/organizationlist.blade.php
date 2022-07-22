<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CUST Internship Portal</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/all.min.css') }}"/>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('css/my.css') }}">
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <!-- Scripts -->
        <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>

        
    </head>
    <body class="antialiased">
        <nav class="navbar navbar-light fixed-top bg-light">
            <a class="navbar-brand font-weight-bold" href="{{ url('/coordinator/dashboard') }}">
                <img src="{{ asset('image/cust.jpg') }}" class="imgcircle" width="30" height="30" class="d-inline-block align-top" alt="">
                CUST Internship Portal
            </a>
            <div class="btn-group">
                <button class="btn btn-info">Working Term: {{ session('term') }}</button>&nbsp
                <button class="btn btn-warning">Term: {{ $term->term_name }}</button>
            </div>
            <div class="btn-group">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $root->coordinators->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('/coordinator/changepassword') }}">Change password</a>
                    <a class="dropdown-item" href="{{ url('/coordinator/logout') }}">Log out</a>
                </div>
            </div>
        </nav>
        <div class="container" style="margin-top:57px;">
            <h1>Student Progress Report</h1>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <h5 class="card-header bg-dark text-white text-center">Students Information</h5>
                        <div class="card-body">
                            <p class="card-title font-weight-bold custFontColor">
                                Here are the organization list students working.
                            </p>
                            <hr class="my-4"/>
                            @if (session('message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @error('description')
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @enderror
                            <label>Search Student</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                                </div>
                                <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search Student (Reg No.)..."/><br/>
                            </div>
                            <div class="scroller">
                                <table class="table" id="myTable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Registration No</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Student Contact No</th>
                                            <th scope="col">Offer Letter</th>
                                            <th scope="col">Documents</th>
                                            <th scope="col">Internal Evaluator</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student as $s)
                                        @if(isset($s->offer_letter))
                                        <tr>
                                            <td>{{ $s->registration_no }}</td>
                                            <td>{{ $s->students->name }}</td>
                                            <td>{{ $s->students->contact_no }}</td>
                                            <td>
                                                @if(isset($s->offer_letter))
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#{{$s->registration_no}}_offerLetter">
                                                    <i class="fas fa-list-alt"></i>
                                                    Offer Letter
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($s->document_uploaded_date))
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#{{$s->registration_no}}_certificate">
                                                    <i class="fas fa-list-alt"></i>
                                                    Documents
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($s->evaluator_id))
                                                    {{ $s->evaluators->name }}
                                                @else
                                                @if(isset($s->offer_letter_status) && isset($s->document_status))
                                                    @if($s->offer_letter_status == 'approved' && $s->document_status == 'approved')
                                                    <a href="/coordinator/selectviva/{{ $s->registration_no}}/{{ $s->term_name }}" class="btn btn-danger" role="button">
                                                        <i class="fas fa-user"></i>
                                                        Assign Viva
                                                    </a>
                                                    @endif
                                                @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="{{$s->registration_no}}_offerLetter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLabel">Offer Letter</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Registration No</strong>: {{ $s->registration_no }}<br/>
                                                                <strong>Name</strong>: {{ $s->students->name }}<br/>
                                                                <br/><br/>
                                                                <strong>Organization NTN No</strong>: {{ $s->organization_ntn_no }}<br/>
                                                                <strong>Organization Name</strong>: {{ $s->organization_name }}<br/>
                                                                <strong>Organization Email</strong>: {{ $s->organization_email }}<br/>
                                                                <strong>Organization Website</strong>: <a href="{{ $s->organization_website }}" target="_blank">{{ $s->organization_website }}</a><br/>
                                                                <strong>Organization Contact</strong>: {{ $s->organization_contact }}<br/>
                                                                <strong>Organization Address</strong>: {{ $s->organization_address }}<br/>
                                                                <br/><br/>
                                                                <strong>Supervisor Name</strong>: {{ $s->supervisor_name }}<br/>
                                                                <strong>Supervisor Email</strong>: {{ $s->supervisor_email }}<br/>
                                                                <strong>Supervisor Designation</strong>: {{ $s->supervisor_designation }}<br/>
                                                                <strong>Supervisor Contact</strong>: {{ $s->supervisor_contact }}<br/>
                                                                <strong>Supervisor Department</strong>: {{ $s->supervisor_department }}<br/>
                                                                <br/><br/>
                                                                <strong>Internship Start Date</strong>: {{ Carbon\Carbon::parse($s->start_date)->toformattedDateString() }}<br/>
                                                                <strong>Internship End Date</strong>: {{ Carbon\Carbon::parse($s->end_date)->toformattedDateString() }}<br/>
                                                                <br/><br/>
                                                                <strong>Uploaded Date</strong>: {{ Carbon\Carbon::parse($s->offer_letter_uploaded_date)->toformattedDateString() }}<br/>
                                                                <strong>Status</strong>: {{ $s->offer_letter_status }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <embed src="{{ asset($s->offer_letter) }}" width="500" height="600" type="application/pdf"/>
                                                            </div>
                                                        </div>
                                                        <hr class="my-4"/>                                                        
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                            @if(session('term') == $term->term_name)
                                                            <form action="{{ url('/coordinator/offerletter_status/'.$s->registration_no) }}" method="post">
                                                                @csrf
                                                                <fieldset>
                                                                    <legend>Remarks</legend>
                                                                    <div class="form-group">
                                                                        <textarea class="form-control" id="remarked" name="description" rows="4">Congratulations, you're offer letter has been accepted.</textarea>
                                                                    </div>
                                                                </fieldset>
                                                                <hr class="my-4"/>
                                                                <div class="row">
                                                                    <div class="col-md-6 text-center">
                                                                        <input type="radio" style="height:30px;width:30px;" id="approved" name="status" value="approved" onclick="changeText1()"/>
                                                                        <label for="approved" class="h3">Approve</label>
                                                                    </div>
                                                                    <div class="col-md-6 text-center">
                                                                        <input type="radio" style="height:30px;width:30px;" id="rejected" name="status" value="rejected" onclick="changeText2()"/>
                                                                        <label for="rejected" class="h3">Reject</label>
                                                                    </div>
                                                                    @error('grade')
                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <hr class="my-4"/>
                                                                <button type="submit" class="btn btn-danger">Submit</button>
                                                            </form>
                                                            @endif
                                                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="{{$s->registration_no}}_certificate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="exampleModalLabel">Internship Documents</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row text-center">
                                                            <div class="col-md-12">
                                                                <strong>Registration No</strong>: {{ $s->registration_no }}<br/>
                                                                <strong>Name</strong>: {{ $s->students->name }}<br/>
                                                            </div>
                                                        </div>
                                                        <hr class="my-4"/>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <h3 class="text-center">Internship Report</h3>
                                                                <embed src="{{ asset($s->internship_report) }}" width="350" height="400" type="application/pdf"/>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <h3 class="text-center">Internship Certificate</h3>
                                                                @if(strpos($s->internship_completion_certificate, "png") != 0)
                                                                <img src="{{ asset($s->internship_completion_certificate) }}" width="350" height="300"/>
                                                                @endif
                                                                @if(strpos($s->internship_completion_certificate, "jpg") != 0)
                                                                <img src="{{ asset($s->internship_completion_certificate) }}" width="350" height="300"/>
                                                                @endif
                                                                @if(strpos($s->internship_completion_certificate, "pdf") != 0)
                                                                <embed src="{{ asset($s->internship_completion_certificate) }}" width="350" height="400" type="application/pdf"/>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-4">
                                                                <h3 class="text-center">Evaluation Performa</h3>
                                                                <embed src="{{ asset($s->internship_evaluation_performa) }}" width="350" height="400" type="application/pdf"/>
                                                            </div>
                                                        </div>
                                                        <hr class="my-4"/>                                                        
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <br/><br/>
                                                                <strong>Documents Uploaded date</strong>: {{ Carbon\Carbon::parse($s->document_uploaded_date)->toformattedDateString() }}<br/>
                                                                <strong>Status</strong>: {{ $s->document_status }}<br/>
                                                            @if(session('term') == $term->term_name)
                                                            <br/><br/>
                                                            <form action="{{ url('/coordinator/internshipcompletion_status/'.$s->registration_no) }}" method="post">
                                                                @csrf
                                                                <fieldset>
                                                                    <legend>Remarks</legend>
                                                                    <div class="form-group">
                                                                        <textarea class="form-control" name="description" rows="4">Congratulations, you're all documents has been accepted.</textarea>
                                                                    </div>
                                                                </fieldset>
                                                                <button type="submit" name="status" value="approved" class="btn btn-success">Approve</button>
                                                                <button type="submit" name="status" value="rejected" class="btn btn-danger">Reject</button>
                                                            </form>
                                                            @endif
                                                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a href="{{ url('coordinator/dashboard') }}" class="btn btn-lg btn-danger">Go Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/my.js') }}"></script>
        <script src="{{ asset('js/approval.js') }}"></script>
    </body>
</html>
