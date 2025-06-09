@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Welcome</div>

                <div class="card-body">
                    <p>Welcome to the CKP-KofA Network!</p>
                    
                    <div class="mt-4">
                        <h4>Test Links</h4>
                        <ul>
                            <li><a href="{{ url('/mobile-app') }}">Mobile App Download Page</a></li>
                            <li><a href="{{ asset('downloads/ckp-kofa-app.apk') }}">Direct APK Download</a></li>
                            <li><a href="{{ url('/test-download.php') }}">Test APK Access</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 