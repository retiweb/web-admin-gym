<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CheckIn;
use Carbon\Carbon;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('refresh-visitor-table')]
    public function refreshTable(){

    }

    public function getCheckInsProperty()
    {
        return CheckIn::with(['member', 'transaction'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('member', function ($memberQuery) {
                        $memberQuery->where('name', 'like', '%' . $this->search . '%')->orWhere('member_code', 'like', '%' . $this->search . '%');
                    })->orWhereHas('transaction', function ($transactionQuery) {
                        $transactionQuery->where('guest_name', 'like', '%' . $this->search . '%')->orWhere('invoice_no', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy('check_in_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return $this->view()->title('Visitor Page');
    }
};
?>

<div class="p-4 sm:p-8">

    <!-- Header & Search Section -->
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-4 sm:p-5 rounded-xl border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Check-In History</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Monitor daily arrivals of members and walk-in guests.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name or code..."
                    class="w-full ps-10 pe-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200 text-gray-900 shadow-sm" />
            </div>

            <button onclick="document.getElementById('modal-scan-barcode').showModal()" type="button"
                class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-200 text-sm whitespace-nowrap">
                <span>Input Code Visitor</span>
            </button>
        </div>
    </div>

    <!-- Wrapper Table -->
    <div class="bg-white border border-gray-100 shadow-sm rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Check-In Time</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Visitor Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Access Code</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    @forelse($this->checkIns as $checkIn)
                        <tr wire:key={{ $checkIn->id }} class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ Carbon::parse($checkIn->check_in_at)->format('H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ Carbon::parse($checkIn->check_in_at)->format('d M Y') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                @if ($checkIn->member_id)
                                    {{ $checkIn->member->name }}
                                @elseif($checkIn->transaction_id)
                                    {{ $checkIn->transaction->guest_name ?? 'Guest' }}
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if ($checkIn->member_id)
                                    <span
                                        class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-semibold tracking-wide">Member</span>
                                @elseif($checkIn->transaction_id)
                                    <span
                                        class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-semibold tracking-wide">Daily
                                        Pass</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-gray-600 font-mono text-xs">
                                @if ($checkIn->member_id)
                                    {{ $checkIn->member->member_code }}
                                @elseif($checkIn->transaction_id)
                                    {{ $checkIn->transaction->invoice_no }}
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button type="button" wire:click="$dispatch('detail-visitor', { id: {{ $checkIn->id }} })"
                                   class="text-indigo-500 hover:text-indigo-700 font-medium transition-colors cursor-pointer">
                                   Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-base font-medium text-gray-500">No check-in records found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->checkIns->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $this->checkIns->links() }}
            </div>
        @endif
    </div>

    <livewire:admin.visitor.create/>
    <livewire:admin.visitor.show/>


    <script>
        document.addEventListener('livewire:initialized', function(){
            Livewire.on('open-modal-detail-visitor', () => {
                document.getElementById('modal-detail-checkin').showModal();
            });
        });
    </script>
</div>


