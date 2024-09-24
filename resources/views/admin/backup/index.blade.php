@extends('layouts.user_type.auth')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-8">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Changes</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->model }}</td>
                            <td>
                                @php
                                    $changes = json_decode($log->changes, true);
                                @endphp
                                @if($changes)
                                    <ul>
                                        @foreach($changes as $field => $value)
                                            <li><strong>{{ $field }}:</strong> {{ is_array($value) ? implode(' -> ', $value) : $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    No changes
                                @endif
                            </td>
                            <td>{{ $log->full_name ?? 'System' }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->created_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $auditLogs->links('pagination::bootstrap-4') }}
                
            </div>

            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->


</div>


@endsection
