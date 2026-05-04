<div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="px-4 py-4 bg-dark text-white text-center">
            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-2"
                 style="width:60px;height:60px;">
                <i class="bi bi-person-fill fs-3 text-white"></i>
            </div>
            <div class="fw-semibold">{{ auth()->user()->name }}</div>
            <small class="text-muted" style="color:#aaa !important;">{{ auth()->user()->email }}</small>
        </div>

        <nav class="py-2">
            <a href="{{ route('shop.account.orders') }}"
               class="d-flex align-items-center gap-2 px-4 py-3 text-decoration-none {{ request()->routeIs('shop.account.orders') ? 'bg-light fw-semibold text-dark' : 'text-secondary' }}"
               style="border-left: 3px solid {{ request()->routeIs('shop.account.orders') ? '#212529' : 'transparent' }};">
                <i class="bi bi-bag-check"></i> My Orders
            </a>
            <a href="{{ route('shop.merchandise.index') }}"
               class="d-flex align-items-center gap-2 px-4 py-3 text-decoration-none text-secondary">
                <i class="bi bi-shop"></i> Browse Shop
            </a>
            <a href="{{ route('shop.cart.index') }}"
               class="d-flex align-items-center gap-2 px-4 py-3 text-decoration-none text-secondary">
                <i class="bi bi-cart3"></i> View Cart
            </a>
            <hr class="my-1 mx-3">
            <a href="{{ route('consumer') }}"
               class="d-flex align-items-center gap-2 px-4 py-3 text-decoration-none text-secondary">
                <i class="bi bi-house"></i> Dashboard
            </a>
        </nav>
    </div>
</div>
