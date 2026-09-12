<?php

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    #[Session]
    public $search = '';

    #[On('refresh-transaction-table')]
    public function refreshTable() {}

    public function getTransactionsProperty()
    {
        return Transaction::with(['member', 'package'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_no', 'ilike', '%' . $this->search . '%')
                        ->orWhereHas('member', function ($memberQuery) {
                            $memberQuery->where('name', 'ilike', '%' . $this->search . '%');
                        })
                        ->orWhereHas('package', function ($packageQuery) {
                            $packageQuery->where('name', 'ilike', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return $this->view()->title('Transaction Page');
    }
};
?>

<div class="w-full">
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-4 sm:p-5 rounded-xl border border-gray-100 shadow-sm">

        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Gym Transactions List</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage and review all member payment transactions.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search transactions..."
                    class="w-full ps-10 pe-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200 text-gray-900 shadow-sm" />
            </div>

            <button onclick="document.getElementById('modal-tambah-transaction').showModal()" type="button"
                class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-200 text-sm whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Add Data</span>
            </button>

        </div>
    </div>

    <!-- Wrapper Tabel -->
    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


        <div wire:loading.flex
            class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm">
            <div class="flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-md ring-1 ring-slate-100">
                <svg class="h-5 w-5 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm font-medium text-slate-600">Memuat data...</span>
            </div>
        </div>

        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Invoice</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Member</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Package</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Total / Metode</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Periode Aktif</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">

                @forelse ($this->transactions as $transaction)
                    <tr wire:key={{ $transaction->id }} class="hover:bg-slate-50/80 transition-colors duration-200">


                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ $transaction->invoice_no }}
                        </td>


                        <td class="px-6 py-4 text-slate-700">
                            {{ optional($transaction->member)->name ?? 'Non Member' }}
                        </td>


                        <td class="px-6 py-4 text-slate-700">
                            <span
                                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                {{ optional($transaction->package)->name ?? 'Non Package' }}
                            </span>
                        </td>


                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">Rp
                                {{ number_format($transaction->total_payment, 0, ',', '.') }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $transaction->payment_method }}</div>
                        </td>


                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-500">Mulai: <span
                                    class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($transaction->active_from)->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">Berakhir: <span
                                    class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($transaction->active_until)->format('d M Y') }}</span>
                            </div>
                        </td>

                        <!-- Kolom Aksi -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-4">
                                <button wire:click="$dispatch('detail-transaction', {id: {{ $transaction->id }}})"
                                    class="text-indigo-500 hover:text-indigo-700 font-medium transition-colors cursor-pointer">Detail</button>
                                <button wire:click="$dispatch('delete-transaction', {id: {{ $transaction->id }}})"
                                    class="text-rose-400 hover:text-rose-600 font-medium transition-colors cursor-pointer">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 mb-3 text-slate-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                <span class="text-sm">Belum ada data transaksi yang dicatat.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>


    <div class="mt-4">
        {{ $this->transactions->links() }}
    </div>

    <livewire:admin.transaction.create />
    <livewire:admin.transaction.show />
    <livewire:admin.transaction.delete />


    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-modal-detail-transaction', () => {
                document.getElementById('modal-detail-transaction').showModal();
            });

            Livewire.on('open-modal-delete-transaction', () => {
                document.getElementById('modal-delete-transaction').showModal();
            });

            Livewire.on('success_transaction_create', (data) => {
                Swal.fire({
                    title: "Success",
                    text: "Transaction successfully recorded.",
                    icon: "success"
                });
            });

            Livewire.on('failed_transaction_create', (data) => {
                Swal.fire({
                    title: "Failed",
                    text: "A system error occurred. Failed to add the transaction.",
                    icon: "error"
                });
            });

            Livewire.on('success_transaction_delete', (data) => {
                Swal.fire({
                    title: "Success",
                    text: "Transaction deleted",
                    icon: "success"
                });
            });

            Livewire.on('failed_transaction_delete', (data) => {
                Swal.fire({
                    title: "Failed",
                    text: "A system error occurred. Failed to delete the transaction.",
                    icon: "error"
                });
            });
        });
    </script>
</div>
