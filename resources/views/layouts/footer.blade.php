<footer class="bg-dark text-white text-center py-1 w-100">
    @php
        $isAdmin = auth()->check() && optional(auth()->user()->role)->role_name === 'Admin';
        $footerText = $isAdmin ? __('messages.footer_admin') : __('messages.footer_user');
    @endphp
    <p class="mb-0">
        {!! str_replace(':year', date('Y'), $footerText) !!}
    </p>
</footer>
