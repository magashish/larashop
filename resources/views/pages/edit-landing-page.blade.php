@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Page / Edit') }}</div>
                <div class="card-body">
                    {{-- Status and Error Alerts remain here --}}
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

                            {{-- Page Title Section --}}
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Page Title</div>
                                    <div class="card-body">
                                        <label for="title" class="form-label fw-semibold">Page Title</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- 1. Hero & Banner Section --}}
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Hero & Banner Section</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 border-end">
                                                <label class="form-label fw-bold">Hero Image</label>
                                                <input type="file" name="landing_hero_image" class="form-control mb-2">
                                                @if(!empty($metaData['landing_hero_image']))
                                                <div class="mt-2 p-2 border rounded bg-light">
                                                    <img src="{{ asset('storage/'.$metaData['landing_hero_image']) }}" class="img-thumbnail" style="height: 100px;">
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input" type="checkbox" name="remove_landing_hero_image" id="rem_hero">
                                                        <label class="form-check-label text-danger small" for="rem_hero text-danger">Remove Image</label>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Banner Slider (Multiple)</label>
                                                <input type="file" name="landing_banner_slider[]" class="form-control" multiple>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach(json_decode($metaData['landing_banner_slider'] ?? '[]', true) as $index => $img)
                                                    <div class="text-center border p-1 rounded bg-light">
                                                        <img src="{{ asset('storage/'.$img) }}" style="height: 60px;" class="d-block mb-1">
                                                        <input type="checkbox" name="remove_landing_banner_slider[]" value="{{ $img }}">
                                                        <span class="small text-danger">Del</span>
                                                        <input type="hidden" name="landing_banner_slider_existing[]" value="{{ $img }}">
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Banner Mobile Slider (Multiple)</label>
                                                <input type="file" name="landing_banner_mobile_slider[]" class="form-control" multiple>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach(json_decode($metaData['landing_banner_mobile_slider'] ?? '[]', true) as $index => $img)
                                                    <div class="text-center border p-1 rounded bg-light">
                                                        <img src="{{ asset('storage/'.$img) }}" style="height: 60px;" class="d-block mb-1">
                                                        <input type="checkbox" name="remove_landing_banner_mobile_slider[]" value="{{ $img }}">
                                                        <span class="small text-danger">Del</span>
                                                        <input type="hidden" name="landing_banner_mobile_slider_existing[]" value="{{ $img }}">
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Package Settings --}}
                            <div class="col-12 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-dark text-white">Package Settings</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label>Package Title</label>
                                            <input type="text" name="landing_package_title" class="form-control" value="{{ old('landing_package_title', $metaData['landing_package_title'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Package Subtitle</label>
                                            <input type="text" name="landing_package_subtitle" class="form-control" value="{{ old('landing_package_subtitle', $metaData['landing_package_subtitle'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Package Image</label>
                                            <input type="file" name="landing_package_image" class="form-control">
                                            @if(!empty($metaData['landing_package_image']))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/'.$metaData['landing_package_image']) }}" class="img-thumbnail" style="height: 80px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_landing_package_image" id="rem_pkg">
                                                    <label class="form-check-label text-danger small" for="rem_pkg">Remove</label>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Worth Section --}}
                            <div class="col-12 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-dark text-white">Worth Section</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label>Worth Title</label>
                                            <input type="text" name="landing_worth_title" class="form-control" value="{{ old('landing_worth_title', $metaData['landing_worth_title'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Worth Subtitle</label>
                                            <input type="text" name="landing_worth_subtitle" class="form-control" value="{{ old('landing_worth_subtitle', $metaData['landing_worth_subtitle'] ?? '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Worth Slider (Images)</label>
                                            <input type="file" name="landing_worth_slider[]" class="form-control" multiple>
                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                @foreach(json_decode($metaData['landing_worth_slider'] ?? '[]', true) as $img)
                                                <div class="text-center border p-1 rounded">
                                                    <img src="{{ asset('storage/'.$img) }}" style="height: 50px;" class="d-block mb-1">
                                                    <input type="checkbox" name="remove_landing_worth_slider[]" value="{{ $img }}">
                                                    <span class="small text-danger">Del</span>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Video Section --}}
                            <div class="col-12 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-dark text-white">Video Section</div>
                                    <div class="card-body">
                                        <label>Title</label>
                                        <input type="text" name="landing_real_title" class="form-control mb-2" value="{{ old('landing_real_title', $metaData['landing_real_title'] ?? '') }}">
                                        
                                        <label>Subtitle</label>
                                        <input type="text" name="landing_real_subtitle" class="form-control mb-3" value="{{ old('landing_real_subtitle', $metaData['landing_real_subtitle'] ?? '') }}">


                                        <label>Landing Video Poster</label>
                                        <input type="file" name="landing_real_video_poster" class="form-control mb-3">
                                        @if(!empty($metaData['landing_real_video_poster']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/'.$metaData['landing_real_video_poster']) }}" class="img-thumbnail" style="height: 80px;">
                                            <div class="form-check">
                                                <input type="checkbox" name="landing_real_video_poster" class="form-check-input">
                                                <label class="small text-danger">Remove</label>
                                            </div>
                                        </div>
                                        @endif
                                        <label>Video File (MP4/MOV)</label>
                                        <input type="file" name="landing_real_video_src" class="form-control mb-2">
                                        @if(!empty($metaData['landing_real_video_src']))
                                        <div class="mt-3">
                                            <video controls class="w-100 rounded border" style="max-height: 250px; background: #000;">
                                                <source src="{{ asset('storage/'.$metaData['landing_real_video_src']) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="mt-2 text-danger">
                                                    <input type="checkbox" name="landing_real_video_src_remove" value="1"> Remove Current Video
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- 5. Past Winners --}}
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-dark text-white">Past Winners & Others</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label>Winner Title</label>
                                                <input type="text" name="landing_past_winner_title" class="form-control" value="{{ old('landing_past_winner_title', $metaData['landing_past_winner_title'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Subtitle</label>
                                                <input type="text" name="landing_past_winner_subtitle" class="form-control" value="{{ old('landing_past_winner_subtitle', $metaData['landing_past_winner_subtitle'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Image</label>
                                                <input type="file" name="landing_past_winner_image" class="form-control">
                                                @if(!empty($metaData['landing_past_winner_image']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['landing_past_winner_image']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_landing_past_winner_image" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label>Winner Slider (Images)</label>
                                                <input type="file" name="landing_past_winner_slider[]" class="form-control" multiple>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach(json_decode($metaData['landing_past_winner_slider'] ?? '[]', true) as $img)
                                                        <div class="text-center border p-1 rounded">
                                                            <img src="{{ asset('storage/'.$img) }}" style="height: 50px;" class="d-block mb-1">
                                                            <input type="checkbox" name="remove_landing_past_winner_slider[]" value="{{ $img }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 6. Harcourt Community --}}
                                <div class="col-12 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-dark text-white">Supporting Harcourt Community</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label>Harcourt Title</label>
                                                <input type="text" name="landing_harcourt_title" class="form-control" value="{{ old('landing_harcourt_title', $metaData['landing_harcourt_title'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Subtitle</label>
                                                <input type="text" name="landing_harcourt_subtitle" class="form-control" value="{{ old('landing_harcourt_subtitle', $metaData['landing_harcourt_subtitle'] ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt description</label>
                                                <textarea class="form-control" id="banner_text" name="landing_harcourt_description" rows="2">{{ old('landing_harcourt_description', $metaData['landing_harcourt_description'] ?? '') }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Image 1</label>
                                                <input type="file" name="landing_harcourt_image_1" class="form-control">
                                                @if(!empty($metaData['landing_harcourt_image_1']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['landing_harcourt_image_1']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_landing_harcourt_image_1" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <label>Harcourt Image 2</label>
                                                <input type="file" name="landing_harcourt_image_2" class="form-control">
                                                @if(!empty($metaData['landing_harcourt_image_2']))
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/'.$metaData['landing_harcourt_image_2']) }}" class="img-thumbnail" style="height: 80px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="remove_landing_harcourt_image_2" class="form-check-input">
                                                            <label class="small text-danger">Remove</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- End Row --}}

                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-save"></i> Update Page
                            </button>
                            <a href="{{ route('pages.index') }}" class="btn btn-secondary ms-2 mt-3">Cancel</a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection