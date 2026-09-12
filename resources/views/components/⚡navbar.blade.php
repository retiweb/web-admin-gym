<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $title;

    public function mount($title = null)
    {
        $this->title = $title;
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();

        session()->regenerateToken();

        // Updated to match your actual login route path
        return $this->redirect('/auth/login', navigate: true);
    }
};
?>

<header class="flex items-center justify-between px-6 bg-white border-b border-gray-200 h-16 shrink-0 z-10">
    <div class="text-lg font-semibold text-gray-700">
        {{ $title ?? 'Menu' }}
    </div>

    <div class="flex items-center space-x-4">
        <!-- User Info -->
        <div class="flex items-center space-x-3">
            <span class="text-sm font-medium text-gray-700">Hello, {{ Auth::user()->name }}</span>
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">R</div>
        </div>

        <!-- Logout Button -->
        <button type="button" @click="confirmLogout"
            class="flex items-center gap-1.5 text-sm font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors duration-200"
            title="Exit Application">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
            <span>Logout</span>
        </button>
    </div>
</header>

@push('scripts')
    <script>
        window.confirmLogout = function() {
            Swal.fire({
                title: 'Logout Confirmation',
                text: "Are you sure you want to log out of the application?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Logout!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('logout');
                }
            });
        }
    </script>
@endpush
