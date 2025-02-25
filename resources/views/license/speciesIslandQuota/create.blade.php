@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Create Species Island Quota</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('species-island-quotas.quota.store') }}" id="quota-form">
                        @csrf

                        <div class="form-group row mb-4">
                            <label for="species_id" class="col-md-4 col-form-label text-md-right">Species</label>
                            <div class="col-md-8">
                                <select name="species_id[]" id="species_id" class="form-control" multiple required>
                                    <option disabled>Select Species</option>
                                    @foreach($species as $id => $speciesItem)
                                        <option value="{{ $id }}">{{ $speciesItem }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="islands-container">
                            <div class="form-group row mb-4">
                                <label for="island_id" class="col-md-4 col-form-label text-md-right">Islands with Quotas</label>
                                <div class="col-md-8">
                                    <table class="table table-striped table-bordered" id="islands-table">
                                        <thead>
                                            <tr>
                                                <th>Island</th>
                                                <th>Quota</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="island_id[]" class="form-control" required>
                                                        <option value="" disabled selected>Select Island</option>
                                                        @foreach($islands as $id => $island)
                                                            <option value="{{ $id }}">{{ $island }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="island_quota[]" class="form-control" min="0" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary btn-sm" id="add-island">Add Another Island</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="year" class="col-md-4 col-form-label text-md-right">Year</label>
                            <div class="col-md-6">
                                <input id="year" type="number" class="form-control" name="year" value="{{ date('Y') }}" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success">Create Quota</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add new island row
        document.getElementById('add-island').addEventListener('click', function() {
            const tbody = document.querySelector('#islands-table tbody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="island_id[]" class="form-control" required>
                        <option value="" disabled selected>Select Island</option>
                        @foreach($islands as $id => $island)
                            <option value="{{ $id }}">{{ $island }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="island_quota[]" class="form-control" min="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                </td>
            `;
            tbody.appendChild(tr);

            // Show all remove buttons when more than one row exists
            if (tbody.children.length > 1) {
                document.querySelectorAll('.remove-row').forEach(button => {
                    button.style.display = 'block';
                });
            }
        });

        // Remove island row
        document.querySelector('#islands-table tbody').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const tbody = document.querySelector('#islands-table tbody');
                e.target.closest('tr').remove();

                // Hide remove buttons if only one row left
                if (tbody.children.length <= 1) {
                    document.querySelectorAll('.remove-row').forEach(button => {
                        button.style.display = 'none';
                    });
                }
            }
        });
    });
</script>
@endpush

@endsection
