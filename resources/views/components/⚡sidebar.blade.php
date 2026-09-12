<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<aside class="flex- flex-col w-64 text-white bg-gray-800 shrink-0">
    <div class="flex items-center justify-center h-16 text-xl font-bold border-b border-gray-700">
        Selamat Data
        Admin
    </div>

    <nav class="flex-1 px-4 py-4 overflow-y-auto space-y-2">
        <!-- Anda bisa membuat link ini dinamis nanti -->
        <a href="{{ route('dashboard') }}" wire:navigate
            class="block px-4 py-2.5 rounded-lg font-medium text-white hover:bg-gray-700 hover:text-white {{ request()->is('admin/dashboard') ? 'bg-gray-900' : '' }}">Dashboard</a>

        <a href="{{ route('members') }}" wire:navigate
            class="block px-4 py-2.5 rounded-lg font-medium text-white hover:bg-gray-700 hover:text-white {{ request()->is('admin/members') ? 'bg-gray-900' : '' }}">Member</a>

        <a href="{{ route('packages') }}" wire:navigate
            class="block px-4 py-2.5 rounded-lg font-medium text-white hover:bg-gray-700 hover:text-white {{ request()->is('admin/packages') ? 'bg-gray-900' : '' }}">Package</a>

        <a href="{{ route('transactions') }}" wire:navigate
            class="block px-4 py-2.5 rounded-lg font-medium text-white hover:bg-gray-700 hover:text-white {{ request()->is('admin/transactions') ? 'bg-gray-900' : '' }}">Transaction</a>

        <a href="{{ route('visitors') }}" wire:navigate
            class="block px-4 py-2.5 rounded-lg font-medium text-white hover:bg-gray-700 hover:text-white {{ request()->is('admin/visitors') ? 'bg-gray-900' : '' }}">Visitor</a>
    </nav>
</aside>
