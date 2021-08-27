@extends('adminlte.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $project->name }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.projects') }}">Projects</a></li>
                            <li class="breadcrumb-item active">{{ $project->name }}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Description
                            </div>
                            <div class="card-body">
                                {!! $project->description !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Members
                            </div>
                            <div class="card-body">
                                <ol>
                                    @foreach ($project->users as $user)
                                        <li>{{ $user->name }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
