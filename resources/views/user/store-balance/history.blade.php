@extends('layouts.app')

@section('content')

    <x-section-header title="Balance History">
        <x-slot:actions>
            <a href="{{ route('user.store.balance.show') }}" class="btn btn-secondary">
                <i class='bx bx-arrow-back me-1'></i> Back to Overview
            </a>
        </x-slot:actions>
    </x-section-header>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="balanceHistoryTable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Balance</th>
                        <th>Notes</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#balanceHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.store.balance.history') }}",
                columns: [
                    {data: 'date', name: 'created_at'},
                    {data: 'type_badge', name: 'type'},
                    {
                        data: 'payment_method',
                        render: function(data) {
                            return data.charAt(0).toUpperCase() + data.slice(1);
                        }
                    },
                    {
                        data: 'amount_formatted',
                        render: function(data, type, row) {
                            return row.type === 'in' ?
                                '<span class="text-success">+' + data + '</span>' :
                                '<span class="text-danger">-' + data + '</span>';
                        }
                    },
                    {data: 'balance_formatted'},
                    {data: 'notes'},
                    {data: 'created_by.name'}
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
@endpush
