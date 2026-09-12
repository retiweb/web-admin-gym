<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

new #[Layout('layouts::auth')] class extends Component {
    public $email = '';
    public $password = '';

    public function render()
    {
        return $this->view()->title('Login Page');
    }

    public function login()
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $messages = [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is required.',
        ];

        $this->validate($rules, $messages);

        try {
            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                session()->regenerate();

                session()->flash('success_login', 'Login successful. Welcome back, Admin!');
                return $this->redirect('/admin/dashboard', navigate: true);
            }

            $this->dispatch('failed_login', message: 'The email or password you entered is incorrect.');

            $this->addError('email', 'These credentials do not match our records.');
        } catch (\Exception $e) {
            Log::error('Failed to login ' . $e->getMessage());

            $this->dispatch('failed_login', message: $e->getMessage());
        }
    }
};
?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <!-- Card Container -->
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8 sm:p-10">

            <!-- Header section -->
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                <p class="text-gray-500 text-sm">Silakan masukkan email & password Anda</p>
            </div>

            <!-- Form -->
            <form class="space-y-5" wire:submit="login">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" wire:model="email"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                        placeholder="name@example.com" required />
                    <div>
                        @error('email')
                            <span class="text-sm text-red-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="input-group-1" :type="showPassword ? 'text' : 'password'" wire:model="password"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                            placeholder="••••••••" required />
                        <button type="button" class="absolute inset-y-0 inset-e-0 flex items-center pe-3"
                            @click="showPassword = !showPassword">

                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        @error('password')
                            <span class="text-sm text-red-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-blue-200 focus:outline-none transition-all duration-200">
                    <span class="in-data-loading:hidden">Login</span>
                    <span class="not-in-data-loading:hidden">Process...</span>
                </button>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('failed_login', (data) => {
            Swal.fire({
                title: "Gagal",
                text: data.message,
                icon: "error"
            });
        });
    });
</script>
