@php
    $searchTerm  = request('search');
    $business_id = request('busness');
    $hasActiveCategory = false;
@endphp

<div class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 tabs-content-col">
                <nav class="categories-tabs">
                    <div class="scroll-wrapper">
                        <button class="scroll-btn left" aria-label="Scroll left">‹</button>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            @foreach ($categories->sortBy('position') as $category)
                                @php
                                    $organizations        = $category->offers->groupBy('organisation_id');
                                    $organizationIdsArray = $organizations->keys()->toArray();
                                    $totalOrgs            = $organizations->count();
                                    $isActive             = false;

                                    if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                        $isActive = true; $hasActiveCategory = true;
                                    } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                        $isActive = true; $hasActiveCategory = true;
                                    } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrgs > 0) {
                                        $isActive = true; $hasActiveCategory = true;
                                    }

                                    $hasOrgClass = (!empty($searchTerm) && $totalOrgs > 0) ||
                                                   (!empty($business_id) && in_array($business_id, $organizationIdsArray));
                                @endphp

                                <button
                                    class="nav-link {{ $isActive ? 'active' : '' }} {{ $hasOrgClass ? 'has-org' : '' }}"
                                    id="nav-{{ $category->id }}-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-{{ $category->id }}"
                                    type="button"
                                    role="tab"
                                    data-category-id="{{ $category->id }}"
                                    data-load-url="{{ route('category.tab.offers', $category->id) }}"
                                    data-offerclass="{{ $offerclass ?? '' }}"
                                    aria-controls="nav-{{ $category->id }}"
                                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    @if(!empty($category->icon))
                                        <img src="{{ Str::startsWith($category->icon, ['http://','https://']) ? $category->icon : Storage::url($category->icon) }}" alt="{{ $category->name }} icon">
                                    @endif
                                    <p>{{ strtoupper($category->name) }}</p>
                                </button>
                            @endforeach
                        </div>
                        <button class="scroll-btn right" aria-label="Scroll right">›</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @php $hasActiveCategory = false; @endphp
                    @forelse ($categories->sortBy('position') as $category)
                        @php
                            $organizations        = $category->offers->groupBy('organisation_id');
                            $organizationIdsArray = $organizations->keys()->toArray();
                            $totalOrgs            = $organizations->count();
                            $isActive             = false;

                            if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                $isActive = true; $hasActiveCategory = true;
                            } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                $isActive = true; $hasActiveCategory = true;
                            } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrgs > 0) {
                                $isActive = true; $hasActiveCategory = true;
                            }
                        @endphp

                        <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}"
                             id="nav-{{ $category->id }}"
                             role="tabpanel"
                             aria-labelledby="nav-{{ $category->id }}-tab"
                             tabindex="0">
                            <div class="text-center py-5 tab-spinner">
                                <div class="spinner-border text-warning" role="status"></div>
                            </div>
                        </div>
                    @empty
                        <div class="row">
                            <div class="col-lg-12 text-center my-5">
                                <p>No offers found that match your search.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>

