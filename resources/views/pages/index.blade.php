@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Pages / List') }}
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> 
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif


                     <div class="mb-3 text-end">
                        <a href="{{ route('pages.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add a Page
                        </a>
                    </div> 
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                 <th>Title</th>
                                
                                 <th class="text-end">Actions</th>
                             </tr>
                         </thead>
                         <tbody>
                            @foreach ($pages as $page)
                            <tr>
                                <td>{{ $page->title }}</td>
                                <td class="text-end">
                                    <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                                 
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
</div>
@endsection
