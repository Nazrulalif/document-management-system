@extends('layouts.user_type.auth')

@section('content')
<style>
    .scrollable-container {
    max-height: 500px; /* Adjust the height as needed */
    overflow-y: auto; /* Enable vertical scrolling */
    padding-right: 15px; /* Prevent content from hiding behind scrollbar */
}

</style>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">

                <livewire:advance-search /> 
               
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

    </div>

    @endsection
