@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>IP Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>
                        <span class="badge-status badge-{{ $log->action }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>
                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $logs->links() }}</div>
</div>
@endsection
