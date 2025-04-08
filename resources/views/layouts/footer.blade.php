<footer class="bg-dark text-white text-center py-2 mt-4">
    <div class="container">
        @php
            $isAdmin = auth()->check() && optional(auth()->user()->role)->role_name === 'Admin';
        @endphp

        <p class="mb-0">
            © {{ date('Y') }} {{ $isAdmin ? 'Admin Panel' : 'Restaurant desks reservations' }}. All rights reserved.
        </p>
    </div>
</footer>

