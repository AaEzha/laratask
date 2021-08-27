@extends('adminlte.layouts.app')

@section('content')

    @stack('header')

    <div style="padding: 20px">
        {!! $output !!}
    </div>

@endsection

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @foreach ($css_files as $css_file)
        <link rel="stylesheet" href="{{ $css_file }}">
    @endforeach
@endpush

@push('js')
    @foreach ($js_files as $js_file)
        <script src="{{ $js_file }}"></script>
    @endforeach
    <script>
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
        }
    </script>
@endpush
