<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

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
        <div style="margin-top:57px;">
            <div class="sidebar">
                <a href="{{ url('/coordinator/dashboard') }}">Dashboard</a>
                <div href="#" class="dropdown-btn">Term Management 
                    <i class="fa fa-caret-down"></i>
                </div>
                <div class="dropdown-container">
                    <a href="{{ url('/coordinator/changeterm') }}">Change Term</a>
                </div>
                @if(session('term') == $term->term_name)
                <div href="#" class="dropdown-btn">Generate Login 
                    <i class="fa fa-caret-down"></i>
                </div>
                <div class="dropdown-container">
                    <a href="{{ url('/coordinator/uploadfile') }}">Upload from Excel File</a>
                    <a href="{{ url('/coordinator/portallogin') }}">Fetch from portal</a>
                </div>
                <a class="active" href="{{ url('coordinator/sendletter') }}">Send Recommendation Letter</a>
                <a href="{{ url('/coordinator/setannouncement') }}">Announcements</a>
                @endif
            </div>
        </div>
        <div class="content">
            <h1>Send Recommendation letter</h1>
            <hr/>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="card shadow">
                        <h5 class="card-header bg-dark text-white text-center">Recommendation Letter</h5>
                        <div class="card-body">
                            <p class="card-title font-weight-bold custFontColor">
                                Here are the students list.
                            </p>
                            <hr class="my-4">
                            <form action="{{ route('letter') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="h2">Add description:</label>
                                    <p class="text-danger"><strong>Note: </strong>Don't change ${}. The names under the bracket are reserved.</p>
                                    <textarea name="description" rows="10" class="form-control">${name} (${registration_no}) is currently doing ${department} at Capital University of Science and Technology (CUST), Islamabad. He/She has completed 90 Credit hours in this course with flying colors. I have no doubt that the student has the skills, focus, and determination to perform well in further studies.

I recommend this student without reservation for the internship. So far, he/she is a disciplined student and has never been involved in any activity that might have tarnished reputation as a student. 

I wish this student best of luck in every endeavor of life.
                                    </textarea>
                                </div>
                                <hr class="my-4">
                                <div class="form-group">
                                    <label class="h2">Select student registration no:</label>
                                    <div style="float:right;">
                                        <button type="button" class="btn btn-danger" id="checkbutton" onclick="letter()">Select all</button>
                                    </div><br/>
                                    <br/>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fas fa-search"></i></div>
                                        </div>
                                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for registration number..."/><br/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="scroller">
                                        <table class="table" id="myTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col">Registration No</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Select</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($student as $s)
                                                <tr>
                                                    <td>{{ $s->students->registration_no }}</td>
                                                    <td>{{ $s->students->name }}</td>
                                                    <td>
                                                        <input type="checkbox" class="checkmark" name="regno[]" value="{{ $s->students->registration_no }}"/>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <br/><p>Send recommendation letter:&nbsp
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        @error('regno')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="{{ asset('js/lettercheck.js') }}"></script>
    <script src="{{ asset('js/my.js') }}"></script>
</html>
