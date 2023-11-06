@extends('master.client.layout.app')

@section('breadcrumb')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb }}</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">{{ $breadcrumb }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@endsection

@section('content')
<div class="content success-page-cont">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
            
                <!-- Success Card -->
                <div class="card success-card">
                    <div class="card-body">
                        <div class="success-cont">
                            <i class="fas fa-check"></i>
                            <h3>Appointment booked Successfully!</h3>
                            <p>Booking Number : <strong>{{ $data->booking_number }}</strong></p>
                            <p>Appointment booked with <strong>{{ $data->doctor->name }}</strong><br> on <strong>{{ \Carbon\Carbon::parse($data->date)->format('d M Y') }} 
                                {{ \Carbon\Carbon::parse($data->start_time)->format('H:i') }} to {{ \Carbon\Carbon::parse($data->end_time)->format('H:i') }} Wita</strong> at 
                            <strong>{{ $data->hospital->name }}</strong></p>
                            <a href="{{ route('client.home') }}" class="btn btn-primary view-inv-btn">Home</a>
                        </div>
                    </div>
                </div>
                <!-- /Success Card -->
                
            </div>
        </div>
    </div>
</div>
@endsection