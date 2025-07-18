@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/admin/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Advanced Search</h5>
        <!--Search Form -->
        <div class="card-body">
            <form class="dt_adv_search" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control dt-input dt-full-name" data-column="1"
                                    placeholder="Alaric Beslier" data-column-index="0" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Email:</label>
                                <input type="text" class="form-control dt-input" data-column="2"
                                    placeholder="demo@example.com" data-column-index="1" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Post:</label>
                                <input type="text" class="form-control dt-input" data-column="3"
                                    placeholder="Web designer" data-column-index="2" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">City:</label>
                                <input type="text" class="form-control dt-input" data-column="4" placeholder="Balky"
                                    data-column-index="3" />
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Date:</label>
                                <div class="mb-0">
                                    <input type="text" class="form-control dt-date flatpickr-range dt-input"
                                        data-column="5" placeholder="StartDate to EndDate" data-column-index="4"
                                        name="dt_date" />
                                    <input type="hidden" class="form-control dt-date start_date dt-input" data-column="5"
                                        data-column-index="4" name="value_from_start_date" />
                                    <input type="hidden" class="form-control dt-date end_date dt-input"
                                        name="value_from_end_date" data-column="5" data-column-index="4" />
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Salary:</label>
                                <input type="text" class="form-control dt-input" data-column="6" placeholder="10000"
                                    data-column-index="5" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class=" table table-bordered" id="cbrBillDataList">
                <thead class="table-dark">
                    <tr>
                        <th>SN</th>
                        <th>Quarter</th>
                        <th>CBR Ref</th>
                        <th>Branch</th>
                        <th>Customer Basic</th>
                        <th>Customer Name</th>
                        <th>Product Group</th>
                        <th>Deal Reference</th>
                        <th>Start Date</th>
                        <th>Outstanding Amount</th>
                        <th>Interest Rate</th>
                    </tr>
                </thead>
                <tfoot class="table-dark">
                    <tr>
                        <th>SN</th>
                        <th>Quarter</th>
                        <th>CBR Ref</th>
                        <th>Branch</th>
                        <th>Customer Basic</th>
                        <th>Customer Name</th>
                        <th>Product Group</th>
                        <th>Deal Reference</th>
                        <th>Start Date</th>
                        <th>Outstanding Amount</th>
                        <th>Interest Rate</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/admin/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    {{-- <script src="{{ asset('assets/admin/js/tables-datatables-advanced.js') }}"></script> --}}

    <script>
        $(document).ready(function() {

            // Trigger search on input change
            /*$('.dt-input').on('keyup change', function() {
                table.draw();
            });*/

            // flatpickr date range setup (if not already done)
            /*if (typeof flatpickr !== 'undefined') {
                flatpickr('.flatpickr-range', {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 2) {
                            $('[name="value_from_start_date"]').val(flatpickr.formatDate(selectedDates[
                                0], 'Y-m-d'));
                            $('[name="value_from_end_date"]').val(flatpickr.formatDate(selectedDates[1],
                                'Y-m-d'));
                        } else {
                            $('[name="value_from_start_date"]').val('');
                            $('[name="value_from_end_date"]').val('');
                        }
                        table.draw();
                    }
                });
            }*/

            let recordsTotal = 0;
            let table = $('#cbrBillDataList').DataTable({
                iDisplayLength: 10,
                processing: true,
                serverSide: true,
                searching: true,
                lengthChange: true,
                destroy: true,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin"></i> Loading...'
                },
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                return "Details of " + row.data().customer_name;
                            }
                        }),
                        renderer: function(api, rowIdx, columns) {
                            var data = columns
                                .map(function(col) {
                                    return col.title ?
                                        `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                                    <td>${col.title}</td>
                                    <td>${col.data}</td>
                               </tr>` :
                                        "";
                                })
                                .join("");
                            return data ?
                                `<table class="table table-striped table-bordered"><tbody>${data}</tbody></table>` :
                                false;
                        }
                    }
                },
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    method: 'POST',
                    url: "{{ url('/admin/cbr/sbs-3/bill/data/get') }}",
                    data: function(d) {
                        d.recordsTotal = recordsTotal;
                    },
                    dataSrc: function(json) {
                        recordsTotal = json.recordsTotal;
                        return json.data;
                    },
                    error: function(xhr, status, error) {
                        console.error('Datatable Error :', xhr.responseText, status, error);
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'quarter_no',
                        name: 'quarter_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'cbr_ref_no',
                        name: 'cbr_ref_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'branch_code',
                        name: 'branch_code',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'customer_mnemonic',
                        name: 'customer_mnemonic',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'product_group',
                        name: 'product_group',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'deal_reference',
                        name: 'deal_reference',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'outstanding_amount_bdt',
                        name: 'outstanding_amount_bdt',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'interest_rate',
                        name: 'interest_rate',
                        orderable: true,
                        searchable: true
                    }
                ],
                "aaSorting": []

            }); // End DataTable

        }); // End Document Ready
    </script>
@endsection
