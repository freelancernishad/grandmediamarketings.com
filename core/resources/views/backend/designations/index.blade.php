@extends('backend.layout.master')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Designations') }}</h1>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-12 col-12 all-users-table">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>{{ __('Designation List') }}</h5>
                            <button class="btn btn-primary btn-sm add" data-toggle="modal" data-target="#addDesignationModal">{{ __('Add New') }}</button>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Minimum Investment') }}</th>
                                            <th>{{ __('Bonus') }}</th>
                                            <th>{{ __('Commission Level') }}</th>
                                            <th>{{ __('Users') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($designations as $designation)
                                            <tr>
                                                <td>{{ $designation->id }}</td>
                                                <td>{{ $designation->name }}</td>
                                                <td>${{ number_format($designation->minimum_investment, 2) }}</td>
                                                <td>${{ number_format($designation->bonus, 2) }}</td>
                                                <td>{{ $designation->commission_level }}</td>
                                                <td>
                                                    <a href="{{ route('admin.designations.users', $designation->id) }}" class="btn btn-info">
                                                        View Users ( {{ $designation->user_designations_count }} )
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary edit"
                                                        data-toggle="modal"
                                                        data-target="#editDesignationModal"
                                                        data-route="{{ route('admin.designations.update', $designation->id) }}"
                                                        data-designation="{{ $designation }}">
                                                        Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Designation Modal -->
    <div class="modal fade" id="addDesignationModal" tabindex="-1" role="dialog" aria-labelledby="addDesignationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.designations.store') }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add Designation') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="minimum_investment">{{ __('Minimum Investment') }}</label>
                            <input type="number" name="minimum_investment" id="minimum_investment" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bonus">{{ __('Bonus') }}</label>
                            <input type="number" name="bonus" id="bonus" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="commission_level">{{ __('Commission Level') }}</label>
                            <input type="number" name="commission_level" id="commission_level" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Designation Modal -->
    <div class="modal fade" id="editDesignationModal" tabindex="-1" role="dialog" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Designation') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_minimum_investment">{{ __('Minimum Investment') }}</label>
                            <input type="number" name="minimum_investment" id="edit_minimum_investment" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_bonus">{{ __('Bonus') }}</label>
                            <input type="number" name="bonus" id="edit_bonus" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_commission_level">{{ __('Commission Level') }}</label>
                            <input type="number" name="commission_level" id="edit_commission_level" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Show Edit Modal with Data
        $('.edit').on('click', function() {
            const modal = $('#editDesignationModal');
            const data = $(this).data('designation');
            const url = $(this).data('route');

            modal.find('form').attr('action', url);
            modal.find('#edit_name').val(data.name);
            modal.find('#edit_minimum_investment').val(data.minimum_investment);
            modal.find('#edit_bonus').val(data.bonus);
            modal.find('#edit_commission_level').val(data.commission_level);

            modal.modal('show');
        });
    });
</script>
@endpush
