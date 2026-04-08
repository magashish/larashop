@extends('layouts.app') {{-- Assuming you have a layout file like layouts/app.blade.php --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('FAQs') }}</div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('faqs.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New FAQ
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table  align-middle table-hover custom-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @forelse ($faqs as $faq)
                                <tr data-id="{{ $faq->id }}">
                                    <td>
                                        <i class="bi bi-arrows-move me-2 handle" style="cursor: move;"></i>
                                        {{ Str::limit($faq->question, 50) }}</td> 
                                    <td>{!! Str::limit($faq->answer, 70) !!}</td> 
                                    <td class="text-end">
                                        <a href="{{ route('faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                                        <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2r"
                                            onclick="return confirm('Are you sure you want to delete this FAQ?')"><i class="bi bi-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No FAQs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection


