@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Page / Edit') }}</div>
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('pages.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Page Admin
                        </a>
                    </div>

                    <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            {{-- Page Title --}}
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Page Title</div>
                                    <div class="card-body">
                                        <input type="text" name="title" class="form-control"
                                               value="{{ old('title', $page->title) }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Hero & Banner --}}
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Hero & Banner Section</div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="fw-bold">Hero Title</label>
                                                <input type="text" name="mazda_title" class="form-control mb-2" value="{{ old('mazda_title', $metaData['mazda_title'] ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="fw-bold">Hero Subtitle</label>
                                                <input type="text" name="mazda_subtitle" class="form-control mb-2" value="{{ old('mazda_subtitle', $metaData['mazda_subtitle'] ?? '') }}">
                                            </div>



                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="fw-bold">Hero Image</label>
                                                <input type="file" name="mazda_hero_image" class="form-control mb-2">
                                                @if(!empty($metaData['mazda_hero_image']))
                                                    <img src="{{ asset('storage/'.$metaData['mazda_hero_image']) }}" height="100">
                                                    <div>
                                                        <input type="checkbox" name="remove_mazda_hero_image"> Remove
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="fw-bold">Hero Second Image</label>
                                                <input type="file" name="mazda_sub_hero_image" class="form-control mb-2">

                                                @if(!empty($metaData['mazda_sub_hero_image']))
                                                    <img src="{{ asset('storage/'.$metaData['mazda_sub_hero_image']) }}" height="100">
                                                    <div>
                                                        <input type="checkbox" name="remove_mazda_sub_hero_image"> Remove
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Check exclusive offers</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="fw-bold">Offers Title</label>
                                                <input type="text" name="mazda_exclusive_title" class="form-control mb-2" value="{{ old('mazda_exclusive_title', $metaData['mazda_exclusive_title'] ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="form-label fw-semibold">
                                                    select exclusive offers <span class="text-danger">*</span>
                                                </label>
                                                @php
                                                $selectedOfferIds = isset($metaData['mazda_featured_offers'])
                                                ? json_decode($metaData['mazda_featured_offers'], true)
                                                : [];
                                                @endphp

                                                <select
                                                class="form-control featured_offers @error('mazda_featured_offers') is-invalid @enderror"
                                                name="mazda_featured_offers[]"
                                                multiple
                                                required
                                                >
                                                @foreach (get_all_offers() as $offer)
                                                <option value="{{ $offer->id }}"
                                                    {{ in_array(
                                                        $offer->id,
                                                        old('mazda_featured_offers', $selectedOfferIds)
                                                        ) ? 'selected' : '' }}
                                                        >
                                                        {{ $offer->title }}
                                                        @if($offer->organisation)
                                                        — {{ $offer->organisation->name }}
                                                        @endif
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('mazda_featured_offers')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- Package --}}
                             <div class="col-12 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-dark text-white">Package Settings</div>
                                    <div class="card-body">
                                          <div class="row">
                                        <div class="col-md-6 mb-3 border-end">
                                            <label>Package Title</label>
                                            <input type="text" name="mazda_package_title" class="form-control" value="{{ old('mazda_package_title', $metaData['mazda_package_title'] ?? '') }}">
                                        </div>
                                       <div class="col-md-6 mb-3 border-end">
                                            <label>Package Subtitle</label>
                                            <input type="text" name="mazda_package_subtitle" class="form-control" value="{{ old('mazda_package_subtitle', $metaData['mazda_package_subtitle'] ?? '') }}">
                                        </div>
                                    </div>
                                      
                                    </div>
                                </div>
                            </div>



                            {{-- Past Winners --}}
                            {{-- 5. Past Winners --}}
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-dark text-white">Past Winners & Others</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label>Winner Title</label>
                                                <input type="text" name="mazda_past_winner_title" class="form-control" value="{{ old('mazda_past_winner_title', $metaData['mazda_past_winner_title'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Subtitle</label>
                                                <input type="text" name="mazda_past_winner_subtitle" class="form-control" value="{{ old('mazda_past_winner_subtitle', $metaData['mazda_past_winner_subtitle'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Image</label>
                                                <input type="file" name="mazda_past_winner_image" class="form-control">
                                                @if(!empty($metaData['mazda_past_winner_image']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['mazda_past_winner_image']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_mazda_past_winner_image" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Slider (Images)</label>
                                                <input type="file" name="mazda_past_winner_slider[]" class="form-control" multiple>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach(json_decode($metaData['mazda_past_winner_slider'] ?? '[]', true) as $img)
                                                        <div class="text-center border p-1 rounded">
                                                            <img src="{{ asset('storage/'.$img) }}" style="height: 50px;" class="d-block mb-1">
                                                            <input type="checkbox" name="remove_mazda_past_winner_slider[]" value="{{ $img }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- Harcourt --}}
                               {{-- 6. Harcourt Community --}}
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-dark text-white">Supporting Harcourt Community</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label>Harcourt Title</label>
                                                <input type="text" name="mazda_harcourt_title" class="form-control" value="{{ old('mazda_harcourt_title', $metaData['mazda_harcourt_title'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Subtitle</label>
                                                <input type="text" name="mazda_harcourt_subtitle" class="form-control" value="{{ old('mazda_harcourt_subtitle', $metaData['mazda_harcourt_subtitle'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt description</label>
                                                <textarea class="form-control" id="banner_text" name="mazda_harcourt_description" rows="2">{{ old('mazda_harcourt_description', $metaData['mazda_harcourt_description'] ?? '') }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Image 1</label>
                                                <input type="file" name="mazda_harcourt_image_1" class="form-control">
                                                @if(!empty($metaData['mazda_harcourt_image_1']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['mazda_harcourt_image_1']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_mazda_harcourt_image_1" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Image 2</label>
                                                <input type="file" name="mazda_harcourt_image_2" class="form-control">
                                                @if(!empty($metaData['mazda_harcourt_image_2']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['mazda_harcourt_image_2']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_mazda_harcourt_image_2" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>

                        <button class="btn btn-primary mt-3">Update Page</button>
                        <a href="{{ route('pages.index') }}" class="btn btn-secondary mt-3">Cancel</a>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
