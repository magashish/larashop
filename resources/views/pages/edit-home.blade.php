@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Page / Edit') }}</div>
                <div class="card-body">
                    {{-- Display success session message --}}
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- Display validation errors --}}
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

                    {{-- The form with file upload support (enctype) and method spoofing for PUT --}}
                    <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Page Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hero Section Fields -->
                        <h4 class="mt-4">Hero Section</h4>




                         @include('pages.blocks.hero_dynamic_slider') 




                        <div class="row">

                        {{-- <div class="mb-3">
                            <label for="hero_slider" class="form-label fw-semibold">Hero Slider Images</label>
                            <input type="file" class="form-control" id="hero_slider" name="hero_slider[]" multiple>

                        </div>
                        <div class="mt-3">
                            <p class="fw-semibold">Current Images:</p>

                            <div class="row g-3">
                                @php
                                $heroSliderPaths = json_decode($metaData['hero_slider'] ?? '[]', true);
                                @endphp
                                @if (!empty($heroSliderPaths))

                                @foreach($heroSliderPaths as $path)
                                <div class="col-12 col-sm-6 col-md-4 relative">
                                    <div class="card p-2 border position-relative image-card h-100"> 
                                        <a href="{{ Storage::url($path) }}" target="_blank" class="block mb-2 w-full">
                                            <img src="{{ Storage::url($path) }}" alt="Slider Image" class="img-thumbnail w-full h-32 object-cover rounded-md shadow-md">
                                        </a>
                                        <div class="flex items-center mt-2">
                                            <input type="hidden" name="hero_slider_existing[]" value="{{ $path }}" class="keep-checkbox">
                                            <input type="checkbox" name="remove_hero_slider[]" value="{{ $path }}" id="remove_image_{{ Str::slug($path) }}" class="form-checkbox h-5 w-5 text-red-600 rounded remove-checkbox">
                                            <label class="ml-2 text-sm font-semibold text-gray-700 cursor-pointer" for="remove_image_{{ Str::slug($path) }}">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <p class="mt-4 text-sm text-gray-500">Check the box to remove an image.</p>
                                @else
                                <p class="text-gray-500">No existing slider images found.</p>
                                @endif
                            </div>

                        </div>  --}}

                        
                         {{--     <div class="mb-3">
                            <label for="hero_slider_mobile" class="form-label fw-semibold">Hero Slider Mobile Images</label>
                            <input type="file" class="form-control" id="hero_slider_mobile" name="hero_slider_mobile[]" multiple>

                        </div>
                        <div class="mt-3">
                            <p class="fw-semibold">Current Images:</p>

                            <div class="row g-3">
                                @php
                                $heroSliderPaths = json_decode($metaData['hero_slider_mobile'] ?? '[]', true);
                                @endphp
                                @if (!empty($heroSliderPaths))

                                @foreach($heroSliderPaths as $path)
                                <div class="col-12 col-sm-6 col-md-4 relative">
                                    <div class="card p-2 border position-relative image-card h-100"> 
                                        <a href="{{ Storage::url($path) }}" target="_blank" class="block mb-2 w-full">
                                            <img src="{{ Storage::url($path) }}" alt="Slider Image" class="img-thumbnail w-full h-32 object-cover rounded-md shadow-md">
                                        </a>
                                        <div class="flex items-center mt-2">
                                            <input type="hidden" name="hero_slider_mobile_existing[]" value="{{ $path }}" class="keep-checkbox">
                                            <input type="checkbox" name="remove_hero_slider_mobile[]" value="{{ $path }}" id="remove_image_{{ Str::slug($path) }}" class="form-checkbox h-5 w-5 text-red-600 rounded remove-checkbox">
                                            <label class="ml-2 text-sm font-semibold text-gray-700 cursor-pointer" for="remove_image_{{ Str::slug($path) }}">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <p class="mt-4 text-sm text-gray-500">Check the box to remove an image.</p>
                                @else
                                <p class="text-gray-500">No existing slider images found.</p>
                                @endif
                            </div>

                            
                        </div>  --}}



                            {{-- These are now file upload fields --}}
                          {{--   <div class="col-md-6 mb-3">
                                <label for="hero_img_1" class="form-label fw-semibold">Hero Image 1</label>
                                <input type="file" class="form-control" id="hero_img_1" name="hero_img_1">
                                @if (isset($metaData['hero_img_1']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['hero_img_1']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hero_img_2" class="form-label fw-semibold">Hero Image 2</label>
                                <input type="file" class="form-control" id="hero_img_2" name="hero_img_2">
                                @if (isset($metaData['hero_img_2']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['hero_img_2']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="mobile_hero_img" class="form-label fw-semibold">Mobile Hero Image</label>
                                <input type="file" class="form-control" id="mobile_hero_img" name="mobile_hero_img">
                                @if (isset($metaData['mobile_hero_img']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['mobile_hero_img']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div> --}}

                            {{-- <div class="col-md-6 mb-3">
                                <label for="mobile_hero_img" class="form-label fw-semibold">Banner Text</label>
                                <textarea class="form-control" id="banner_text" name="banner_text" rows="2">{{ old('banner_text', $metaData['banner_text'] ?? '') }}</textarea>
                            </div> --}}
                       
                        </div>


                       <!--  <hr>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hero_title_line1" class="form-label fw-semibold">Hero Title Line 1</label>
                                <input type="text" class="form-control" id="hero_title_line1" name="hero_title_line1" value="{{ old('hero_title_line1', $metaData['hero_title_line1'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hero_title_line2" class="form-label fw-semibold">Hero Title Line 2</label>
                                <input type="text" class="form-control" id="hero_title_line2" name="hero_title_line2" value="{{ old('hero_title_line2', $metaData['hero_title_line2'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            {{-- These are now file upload fields --}}
                            <div class="col-md-6 mb-3">
                                <label for="hero_rating_users_img" class="form-label fw-semibold">Rating Users Image</label>
                                <input type="file" class="form-control" id="hero_rating_users_img" name="hero_rating_users_img">
                                @if (isset($metaData['hero_rating_users_img']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['hero_rating_users_img']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hero_rating_icon_img" class="form-label fw-semibold">Rating Icon Image</label>
                                <input type="file" class="form-control" id="hero_rating_icon_img" name="hero_rating_icon_img">
                                @if (isset($metaData['hero_rating_icon_img']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['hero_rating_icon_img']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hero_rating_value" class="form-label fw-semibold">Rating Value</label>
                                <input type="text" class="form-control" id="hero_rating_value" name="hero_rating_value" value="{{ old('hero_rating_value', $metaData['hero_rating_value'] ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hero_subscribers_text" class="form-label fw-semibold">Subscribers Text</label>
                                <input type="text" class="form-control" id="hero_subscribers_text" name="hero_subscribers_text" value="{{ old('hero_subscribers_text', $metaData['hero_subscribers_text'] ?? '') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="hero_roadmap_link" class="form-label fw-semibold">Roadmap Link</label>
                            <input type="text" class="form-control" id="hero_roadmap_link" name="hero_roadmap_link" value="{{ old('hero_roadmap_link', $metaData['hero_roadmap_link'] ?? '') }}">
                        </div>
                    -->






                       {{--  <div class="mb-3">
                            <label for="hero_slider" class="form-label fw-semibold">Hero Slider Images</label>
                            <input type="file" class="form-control" id="hero_slider" name="hero_slider[]" multiple>

                        </div>
                        <div class="mt-3">
                            <p class="fw-semibold">Current Images:</p>

                            <div class="row g-3">
                                @php
                                $heroSliderPaths = json_decode($metaData['hero_slider'] ?? '[]', true);
                                @endphp
                                @if (!empty($heroSliderPaths))

                                @foreach($heroSliderPaths as $path)
                                <div class="col-12 col-sm-6 col-md-4 relative">
                                    <div class="card p-2 border position-relative image-card h-100"> 
                                        <a href="{{ Storage::url($path) }}" target="_blank" class="block mb-2 w-full">
                                            <img src="{{ Storage::url($path) }}" alt="Slider Image" class="img-thumbnail w-full h-32 object-cover rounded-md shadow-md">
                                        </a>
                                        <div class="flex items-center mt-2">
                                            <input type="hidden" name="hero_slider_existing[]" value="{{ $path }}" class="keep-checkbox">
                                            <input type="checkbox" name="hero_slider_remove[]" value="{{ $path }}" id="remove_image_{{ Str::slug($path) }}" class="form-checkbox h-5 w-5 text-red-600 rounded remove-checkbox">
                                            <label class="ml-2 text-sm font-semibold text-gray-700 cursor-pointer" for="remove_image_{{ Str::slug($path) }}">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <p class="mt-4 text-sm text-gray-500">Check the box to remove an image.</p>
                                @else
                                <p class="text-gray-500">No existing slider images found.</p>
                                @endif
                            </div>
                        </div>  --}}

                        <!-- Cash Giveaway Section Fields -->
                        <!-- <h4 class="mt-4">Cash Giveaway Section</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="cash_giveaway_title_line1" class="form-label fw-semibold">Giveaway Title Line 1</label>
                                <input type="text" class="form-control" id="cash_giveaway_title_line1" name="cash_giveaway_title_line1" value="{{ old('cash_giveaway_title_line1', $metaData['cash_giveaway_title_line1'] ?? '') }}">
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="cash_giveaway_title_line2" class="form-label fw-semibold">Giveaway Title Line 2</label>
                                <input type="text" class="form-control" id="cash_giveaway_title_line2" name="cash_giveaway_title_line2" value="{{ old('cash_giveaway_title_line2', $metaData['cash_giveaway_title_line2'] ?? '') }}">
                            </div> --}}
                        </div>
                        <div class="mb-3">
                            {{-- This is now a file upload field --}}
                            <label for="cash_giveaway_image" class="form-label fw-semibold">Giveaway Image</label>
                            <input type="file" class="form-control" id="cash_giveaway_image" name="cash_giveaway_image">
                            @if (isset($metaData['cash_giveaway_image']))
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $metaData['cash_giveaway_image']) }}" alt="Current Image" style="max-height: 100px;">
                                <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                            </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="cash_giveaway_description" class="form-label fw-semibold">Giveaway Description</label>
                            <textarea class="form-control" id="cash_giveaway_description" name="cash_giveaway_description" rows="3">{{ old('cash_giveaway_description', $metaData['cash_giveaway_description'] ?? '') }}</textarea>
                        </div>

                       <div class="mb-3">
                        <p>The prize draw displayed on the home page is pulled automatically from the latest draw created in the <a href="{{ route('prize_draws.index') }}" target="_blank">Prize Draw section</a>. If the current home page prize draw does not update immediately after creating a new draw, please check and confirm that the draw is published correctly in the Prize Draw section. The home page will always reflect the most recently created and active prize draw.</p>
                        {{-- <label for="cash_giveaway_date" class="form-label fw-semibold">Cash Giveaway Date/Time</label>
                            <input type="datetime-local" name="cash_giveaway_date" id="cash_giveaway_date" value="{{ old('cash_giveaway_date', $metaData['cash_giveaway_date'] ?? '') }}" class="form-control">
                            @error('cash_giveaway_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror --}}
                        </div> -->


                        <!-- Tabbed Section Fields -->
                        <h4 class="mt-4">Tabbed Section</h4>
                        <hr>
                        <div class="mb-3">
                            <h5 class="mt-3">How It Works Tab</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work_tab_title_line1" class="form-label fw-semibold">Title Line 1</label>
                                    <input type="text" class="form-control" id="work_tab_title_line1" name="work_tab_title_line1" value="{{ old('work_tab_title_line1', $metaData['work_tab_title_line1'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="work_tab_title_line2" class="form-label fw-semibold">Title Line 2 (Spanned)</label>
                                    <input type="text" class="form-control" id="work_tab_title_line2" name="work_tab_title_line2" value="{{ old('work_tab_title_line2', $metaData['work_tab_title_line2'] ?? '') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="work_tab_paragraph" class="form-label fw-semibold">Paragraph 1</label>
                                <textarea class="form-control" id="work_tab_paragraph" name="work_tab_paragraph" rows="2">{{ old('work_tab_paragraph', $metaData['work_tab_paragraph'] ?? '') }}</textarea>
                            </div>
                         
                            <div class="mb-3">
                                <label for="work_tab_image" class="form-label fw-semibold">Image</label>
                                <input type="file" class="form-control" id="work_tab_image" name="work_tab_image">
                                @if (isset($metaData['work_tab_image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['work_tab_image']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="mt-3">Rewards Tab</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="rewards_tab_title_line1" class="form-label fw-semibold">Title Line 1</label>
                                    <input type="text" class="form-control" id="rewards_tab_title_line1" name="rewards_tab_title_line1" value="{{ old('rewards_tab_title_line1', $metaData['rewards_tab_title_line1'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="rewards_tab_title_line2" class="form-label fw-semibold">Title Line 2 (Spanned)</label>
                                    <input type="text" class="form-control" id="rewards_tab_title_line2" name="rewards_tab_title_line2" value="{{ old('rewards_tab_title_line2', $metaData['rewards_tab_title_line2'] ?? '') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="rewards_tab_paragraph" class="form-label fw-semibold">Paragraph</label>
                                <textarea class="form-control" id="rewards_tab_paragraph" name="rewards_tab_paragraph" rows="3">{{ old('rewards_tab_paragraph', $metaData['rewards_tab_paragraph'] ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="rewards_tab_image" class="form-label fw-semibold">Image</label>
                                <input type="file" class="form-control" id="rewards_tab_image" name="rewards_tab_image">
                                @if (isset($metaData['rewards_tab_image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['rewards_tab_image']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="mt-3">Community Tab</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="community_tab_title_line1" class="form-label fw-semibold">Title Line 1</label>
                                    <input type="text" class="form-control" id="community_tab_title_line1" name="community_tab_title_line1" value="{{ old('community_tab_title_line1', $metaData['community_tab_title_line1'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="community_tab_title_line2" class="form-label fw-semibold">Title Line 2 (Spanned)</label>
                                    <input type="text" class="form-control" id="community_tab_title_line2" name="community_tab_title_line2" value="{{ old('community_tab_title_line2', $metaData['community_tab_title_line2'] ?? '') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="community_tab_paragraph" class="form-label fw-semibold">Paragraph</label>
                                <textarea class="form-control" id="community_tab_paragraph" name="community_tab_paragraph" rows="3">{{ old('community_tab_paragraph', $metaData['community_tab_paragraph'] ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="community_tab_image" class="form-label fw-semibold">Image</label>
                                <input type="file" class="form-control" id="community_tab_image" name="community_tab_image">
                                @if (isset($metaData['community_tab_image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['community_tab_image']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <h5 class="mt-3">Lifestyle Tab</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="lifestyle_tab_title_line1" class="form-label fw-semibold">Title Line 1</label>
                                    <input type="text" class="form-control" id="lifestyle_tab_title_line1" name="lifestyle_tab_title_line1" value="{{ old('lifestyle_tab_title_line1', $metaData['lifestyle_tab_title_line1'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lifestyle_tab_title_line2" class="form-label fw-semibold">Title Line 2 (Spanned)</label>
                                    <input type="text" class="form-control" id="lifestyle_tab_title_line2" name="lifestyle_tab_title_line2" value="{{ old('lifestyle_tab_title_line2', $metaData['lifestyle_tab_title_line2'] ?? '') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="lifestyle_tab_paragraph" class="form-label fw-semibold">Paragraph</label>
                                <textarea class="form-control" id="lifestyle_tab_paragraph" name="lifestyle_tab_paragraph" rows="3">{{ old('lifestyle_tab_paragraph', $metaData['lifestyle_tab_paragraph'] ?? '') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="lifestyle_tab_image" class="form-label fw-semibold">Image</label>
                                <input type="file" class="form-control" id="lifestyle_tab_image" name="lifestyle_tab_image">
                                @if (isset($metaData['lifestyle_tab_image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['lifestyle_tab_image']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>



                            <div class="mb-3">
                                <label for="community_tab_image" class="form-label fw-semibold">Video Poster Image</label>
                                <input type="file" class="form-control" id="hero_video_poster" name="hero_video_poster">
                                @if (isset($metaData['hero_video_poster']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $metaData['hero_video_poster']) }}" alt="Current Image" style="max-height: 100px;">
                                    <small class="d-block text-muted">Current image. Upload a new one to replace it.</small>
                                </div>
                                @endif
                            </div>


                            <div class="mb-3">
                                <label for="hero_video_src" class="form-label fw-semibold">Video </label>
                                <input type="file" class="form-control" id="hero_video_src" accept="video/mp4,video/ogg,video/mov" name="hero_video_src">
                                @if(isset($metaData['hero_video_src']))
                                <div class="mt-4">
                                    <video controls class="w-full rounded-md shadow-md" src="{{ Storage::url($metaData['hero_video_src']) }}"></video>
                                    <div class="flex items-center mt-2">
                                        <input type="checkbox" name="hero_video_src_remove" id="hero_video_src_remove" value="1" class="form-checkbox h-5 w-5 text-red-600 rounded">
                                        <label class="ml-2 text-sm font-semibold text-gray-700" for="hero_video_src_remove">Remove current video</label>
                                    </div>
                                </div>
                                @endif
                            </div>


                        </div>

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




<script>
    document.addEventListener('DOMContentLoaded', () => {
        const removeCheckboxes = document.querySelectorAll('.remove-checkbox');
        removeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (event) => {
                const parentDiv = event.target.closest('.relative');
                const hiddenInput = parentDiv.querySelector('input[type="hidden"]');
                if (event.target.checked) {
                    if (hiddenInput) {
                        hiddenInput.disabled = true;
                    }
                } else {
                    if (hiddenInput) {
                        hiddenInput.disabled = false;
                    }
                }
            });
        });
    });
</script>
@endsection
