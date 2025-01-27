{{-- resources/views/components/data-table.blade.php --}}
@props([
    'id' => 'dataTable',
    'columns' => [],
    'ajaxUrl' => '',
    'actions' => true,
    'editRoute' => '',
    'showRoute' => '',
    'deleteRoute' => '',
    'printRoute' => '',
    'showInvoiceRoute' => '',
    'showIssueLicenseFormRoute' => '',
    'downloadRoute' => '',
    'exportable' => true
])

<div class="container my-5">
    <div class="card">
        <div class="card-body">
            <div class="table-wrapper">
                @if($exportable)
                <div class="mb-4 d-flex justify-content-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary" data-export="excel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </button>
                        <button class="btn btn-outline-primary" data-export="pdf">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </button>
                        <button class="btn btn-outline-primary" data-export="print">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
                @endif
                
                <div class="table-responsive">
                    <table id="{{ $id }}" class="table styled-table">
                        <thead>
                            <tr>
                                @foreach ($columns as $column)
                                    <th>{{ $column['title'] }}</th>
                                @endforeach
                                @if($actions)
                                    <th class="text-center">Actions</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .styled-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.95rem;
    }

    .styled-table thead th {
        background: #2563eb;
        color: #ffffff;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.05em;
        border: none;
    }

    .styled-table thead th:first-child {
        border-top-left-radius: 8px;
    }

    .styled-table thead th:last-child {
        border-top-right-radius: 8px;
    }

    .styled-table tbody tr {
        transition: all 0.2s ease;
    }

    .styled-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .styled-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Action Dropdown Styles */
    .action-dropdown {
        position: relative;
        display: inline-block;
    }

    .action-btn {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: #1d4ed8;
    }

    .action-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        min-width: 160px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        overflow: hidden;
    }

    .action-dropdown:hover .action-dropdown-content {
        display: block;
    }

    .action-dropdown-content a,
    .action-dropdown-content button {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        text-align: left;
        border: none;
        background: none;
        color: #374151;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .action-dropdown-content a:hover,
    .action-dropdown-content button:hover {
        background-color: #f3f4f6;
        color: #2563eb;
    }

    .action-dropdown-content .delete-btn {
        color: #dc2626;
    }

    .action-dropdown-content .delete-btn:hover {
        background-color: #fef2f2;
        color: #dc2626;
    }

    /* Export Buttons */
    .btn-group {
        display: flex;
        gap: 0.5rem;
    }

    .btn-outline-primary {
        border: 1px solid #2563eb;
        color: #2563eb;
        background: transparent;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        background: #2563eb;
        color: white;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .styled-table {
            font-size: 0.875rem;
        }

        .styled-table td,
        .styled-table th {
            padding: 0.75rem;
        }

        .btn-group {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with improved configuration
    const table = $('#{{ $id }}').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ $ajaxUrl }}',
        columns: [
            @foreach ($columns as $column)
                { 
                    data: '{{ $column['data'] }}', 
                    name: '{{ $column['name'] }}',
                    render: {!! isset($column['render']) ? $column['render'] : 'null' !!}
                },
            @endforeach
            @if($actions)
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    if (type === 'export') {
                        return '';
                    }
                    
                    let actions = '<div class="action-dropdown">';
                    actions += '<button class="action-btn">Actions</button>';
                    actions += '<div class="action-dropdown-content">';
                    
                    // Build action links
                    @if($printRoute)
                        actions += `<a href="${replacePlaceholders('{{ $printRoute }}', row)}"><i class="fas fa-print me-2"></i>Print</a>`;
                    @endif
                    
                    @if($editRoute)
                        actions += `<a href="${'{{ $editRoute }}'.replace('__id__', row.id)}"><i class="fas fa-edit me-2"></i>Edit</a>`;
                    @endif
                    
                    @if($showRoute)
                        actions += `<a href="${'{{ $showRoute }}'.replace('__id__', row.id)}"><i class="fas fa-eye me-2"></i>View</a>`;
                    @endif
                    
                    @if($showInvoiceRoute)
                        actions += `<a href="${replacePlaceholders('{{ $showInvoiceRoute }}', row)}"><i class="fas fa-file-invoice me-2"></i>Invoice</a>`;
                    @endif
                    
                    @if($showIssueLicenseFormRoute)
                        if (row.status === 'reviewed') {
                            actions += `<a href="${replacePlaceholders('{{ $showIssueLicenseFormRoute }}', row)}"><i class="fas fa-certificate me-2"></i>Issue License</a>`;
                        }
                    @endif
                    
                    @if($downloadRoute)
                        if (row.status === 'license_issued') {
                            actions += `<a href="${replacePlaceholders('{{ $downloadRoute }}', row)}"><i class="fas fa-download me-2"></i>Download</a>`;
                        }
                    @endif
                    
                    @if($deleteRoute)
                        actions += `
                            <form action="${'{{ $deleteRoute }}'.replace('__id__', row.id)}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn"><i class="fas fa-trash-alt me-2"></i>Delete</button>
                            </form>
                        `;
                    @endif
                    
                    actions += '</div></div>';
                    return actions;
                }
            }
            @endif
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-2"></i>Excel',
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf me-2"></i>PDF',
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-2"></i>Print',
                className: 'btn btn-outline-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });

    // Helper function to replace placeholders
    function replacePlaceholders(route, row) {
        return route.replace(/__([\w]+)__/g, function(match, key) {
            return row[key] || '';
        });
    }

    // Handle delete button clicks
    $(document).on('click', '.delete-btn', function() {
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Custom export button handling
    $('[data-export]').on('click', function() {
        const exportType = $(this).data('export');
        table.button(`.buttons-${exportType}`).trigger();
    });
});
</script>
@endpush