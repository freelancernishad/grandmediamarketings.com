@extends('backend.layout.master')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('User Designations') }}</h1>
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-12 col-12 all-users-table">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>{{ __('User Designation List') }}</h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('User ID') }}</th>
                                            <th>{{ __('User Name') }}</th>
                                            <th>{{ __('Designation') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($userDesignations as $userDesignation)
                                            <tr>
                                                <td>{{ $userDesignation->user->id }}</td>
                                                <td>{{ $userDesignation->user->fullName }}</td>
                                                <td>{{ $userDesignation->designation->name }}</td>
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
@endsection
