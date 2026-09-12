<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CheckIn;

new class extends Component {
    public ?CheckIn $checkIn = null;

    #[On('detail-visitor')]
    public function load(?int $id = null)
    {
        $this->checkIn = CheckIn::with(['member', 'transaction'])->find($id);
        $this->dispatch('open-modal-detail-visitor');
    }


    public function closeModal()
    {
        $this->dispatch('close-modal-detail-visitor');
        $this->checkIn = null;
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-detail-checkin" @open-modal-detail-visitor.window="$el.showModal()"
        @close-modal-detail-visitor.window="$el.close()" x-data="{}" aria-labelledby="dialog-title"
        class="fixed inset-0 z-50 w-full h-full max-w-none max-h-none p-0 m-0 bg-transparent backdrop:bg-transparent overflow-y-auto">

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"
                aria-hidden="true">
            </div>

            <div class="relative z-10 w-full max-w-2xl bg-white rounded-lg shadow-xl overflow-hidden text-left">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-gray-800">Check-In Details</h2>
                        @if ($checkIn)
                            @if ($checkIn->member_id)
                                <span
                                    class="px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Member</span>
                            @elseif ($checkIn->transaction_id)
                                <span
                                    class="px-2.5 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Daily
                                    Pass</span>
                            @endif
                        @endif
                    </div>
                    <button wire:click="closeModal" type="button" class="text-gray-500 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if ($checkIn)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Check-In ID</p>
                                <p class="font-semibold text-gray-800">#{{ $checkIn->id }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Check-In Time</p>
                                <p class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($checkIn->check_in_at)->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Visitor Name</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $checkIn->member->name ?? ($checkIn->transaction->guest_name ?? 'Guest') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Access Code / Invoice</p>
                                <p class="font-mono font-semibold text-green-600">
                                    {{ $checkIn->member->member_code ?? ($checkIn->transaction->invoice_no ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Visitor Type</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $checkIn->member_id ? 'Registered Gym Member' : 'Daily Pass (Walk-in Guest)' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Contact / Status</p>
                                @if ($checkIn->member_id)
                                    <p class="font-semibold text-gray-800">{{ $checkIn->member->phone ?? '-' }}</p>
                                @else
                                    <span
                                        class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">
                                        Paid
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex p-12 justify-center items-center">
                        <h5 class="text-2xl text-gray-400 font-medium">Check-In details not found</h5>
                    </div>
                @endif

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeModal" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </dialog>
</div>
