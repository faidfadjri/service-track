@extends('app')
<link rel="stylesheet" href="{{ asset('assets/css/pages/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/mobile/service.css') }}">
@section('content')
    <h2 class="page-title mb-5">Service Tracking</h2>

    @include('pages.service.mobile')

    @include('components.modals.add')
    @include('components.modals.notes')
    @include('components.modals.add-wo')
    @include('components.modals.add-data')
    @include('components.modals.detail')
    @include('components.datatables.admin')
@endsection
