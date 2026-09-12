<?php

use Livewire\Component;
use App\Models\CheckIn;
use App\Models\Member;
use App\Models\Transaction;

new class extends Component {
    public $scannedCode = '';
    public $scanMessage = '';
    public $scanStatus = '';

     public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function processCheckIn()
    {
        $this->scanMessage = '';
        $this->scanStatus = '';

        if (empty($this->scannedCode)) {
            $this->scanMessage = 'Please enter a code!';
            $this->scanStatus = 'error';
            return;
        }

        if (str_starts_with($this->scannedCode, 'MEM-')) {
            $member = Member::where('member_code', $this->scannedCode)->first();

            if ($member) {
                CheckIn::create([
                    'member_id' => $member->id,
                    'check_in_at' => now(),
                ]);
                $this->scanMessage = "Success! Welcome back, {$member->name}.";
                $this->scanStatus = 'success';
            } else {
                $this->scanMessage = 'Member not found!';
                $this->scanStatus = 'error';
            }
        } elseif (str_starts_with($this->scannedCode, 'INV-')) {
            $transaction = Transaction::where('invoice_no', $this->scannedCode)->first();

            if ($transaction) {
                CheckIn::create([
                    'transaction_id' => $transaction->id,
                    'check_in_at' => now(),
                ]);
                $this->scanMessage = "Success! Welcome, {$transaction->guest_name}.";
                $this->scanStatus = 'success';
            } else {
                $this->scanMessage = 'Invoice not found!';
                $this->scanStatus = 'error';
            }
        } else {
            $this->scanMessage = 'Invalid format! Must start with MEM- or INV-.';
            $this->scanStatus = 'error';
        }

        $this->scannedCode = '';
        $this->dispatch('refresh-visitor-table');
    }
};
?>

<div>
    <dialog id="modal-scan-barcode"
        class="p-0 bg-transparent rounded-xl shadow-2xl backdrop:bg-gray-900/50 m-auto w-full max-w-md" wire:ignore.self
        x-data={}>
        <div class="bg-white rounded-xl overflow-hidden w-full p-6 sm:p-8 relative">

            <button type="button" onclick="document.getElementById('modal-scan-barcode').close()" wire:click="resetForm"
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Header Modal -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Gym Check-In</h2>
                <p class="text-sm text-gray-500 mt-1">Scan barcode or enter Member / Invoice Code</p>
            </div>

            @if ($scanMessage)
                <div
                    class="mb-5 p-3 rounded-lg text-sm font-medium border flex items-center gap-2 {{ $scanStatus === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                    @if ($scanStatus === 'success')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                    <span>{{ $scanMessage }}</span>
                </div>
            @endif

            <!-- Form Input -->
            <form wire:submit.prevent="processCheckIn">
                <div class="mb-5">
                    <label for="scannedCode" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Access Code
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                        </span>
                        <input type="text" id="scannedCode" wire:model="scannedCode" autofocus autocomplete="off"
                            class="w-full ps-10 pe-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200 text-gray-900 font-mono text-lg tracking-wider shadow-sm uppercase placeholder-gray-400 placeholder:text-sm placeholder:tracking-normal"
                            placeholder="e.g., MEM-001 or INV-001">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-scan-barcode').close()"
                        wire:click="resetForm"
                        class="w-1/2 py-2.5 px-4 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-1/2 py-2.5 px-4 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold shadow-sm transition-colors">
                        Process
                    </button>
                </div>
            </form>

        </div>
    </dialog>
</div>
