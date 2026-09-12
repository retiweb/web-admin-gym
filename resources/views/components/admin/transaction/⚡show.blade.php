<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Transaction; // Make sure to import your model

new class extends Component {
    //allow nullable
    public ?Transaction $transaction = null;

    #[On('detail-transaction')]
    public function load($id)
    {
        $this->transaction = Transaction::with(['member', 'package'])->findOrFail($id);

        $this->dispatch('open-modal-detail-transaction');
    }

    public function closeModal()
    {
        $this->transaction = null;
        $this->dispatch('close-modal-detail-transaction');
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-detail-transaction" @close-modal-detail-transaction.window="$el.close()" x-data={}
        aria-labelledby="dialog-title"
        class="fixed inset-0 z-50 w-full h-full max-w-none max-h-none p-0 m-0 bg-transparent backdrop:bg-transparent overflow-y-auto">

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"
                aria-hidden="true">
            </div>

            <!-- Modal Content -->
            <div class="relative z-10 w-full max-w-2xl bg-white rounded-lg shadow-xl overflow-hidden text-left">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Transaction Details</h2>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-red-500 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @if ($transaction)
                    <!-- Modal Body -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Transaction ID</p>
                                <p class="font-semibold text-gray-800">#{{ $transaction->id }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Date</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Customer Name</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $transaction->member->name ?? ($transaction->guest_name ?? 'Unknown Customer') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-1">Package</p>
                                <p class="font-semibold text-gray-800">
                                    {{ $transaction->package->name ?? 'Unknown Package' }}
                                </p>
                            </div>

                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                                <p class="text-xl font-bold text-blue-600">
                                    Rp {{ number_format($transaction->total_payment ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex p-12 justify-center items-center">
                        <h5 class="text-2xl text-gray-400 font-medium">Transaction not found</h5>
                    </div>
                @endif
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </dialog>
</div>
