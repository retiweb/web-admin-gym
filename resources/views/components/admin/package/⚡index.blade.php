<?php

use Livewire\Component;
use App\Models\Package;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    //
    use WithPagination;

    #[Session]
    public $search = '';

    #[On('refresh-package-table')]
    public function refreshTable()
    {

    }

    public function getPackagesProperty()
    {
        return Package::query()->when($this->search, function($query){
            $query->where(function($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%');
                });
        })->orderBy('created_at', 'desc')->paginate(10);
    }

    public function render()
    {
        return $this->view()->title('Package Page');
    }
};
?>

<div class="w-full">
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-4 sm:p-5 rounded-xl border border-gray-100 shadow-sm">

        <!-- Title Section -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">List Package</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage membership packages, pricing, and durations.</p>
        </div>

        <!-- Actions Wrapper (Search & Add Button) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">

            <!-- Styled Livewire Search Input with Icon -->
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 inset-s-0 flex items-center ps-3.5 pointer-events-none text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search packages..."
                    class="w-full ps-10 pe-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200 text-gray-900 shadow-sm" />
            </div>

            <!-- Add Data Button -->
            <button command="show-modal" commandfor="modal-create-package" type="button"
                class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-200 text-sm whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Add Data</span>
            </button>

        </div>
    </div>

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
                    <th scope="col" class="px-6 py-4 font-semibold">Name</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Price</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Duration</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">

                @forelse ($this->packages as $package)
                    <tr wire:key={{ $package->id }} class="hover:bg-slate-50/80 transition-colors duration-200">

                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ $package->name }}
                        </td>

                        <td class="px-6 py-4 font-medium text-slate-700">
                            Rp {{ number_format($package->price, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 text-slate-500">
                            {{ $package->duration_days }} Hari
                        </td>

                        <td class="px-6 py-4">
                            @if ($package->is_active)
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600 ring-1 ring-inset ring-emerald-500/20">
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-600 ring-1 ring-inset ring-rose-500/20">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-4">
                                <button wire:click="$dispatch('edit-package', {id: {{ $package->id }}})"
                                    class="text-indigo-500 hover:text-indigo-700 font-medium transition-colors cursor-pointer">Edit</button>
                                <button wire:click="$dispatch('delete-package', {id: {{ $package->id }}})"
                                    class="text-rose-400 hover:text-rose-600 font-medium transition-colors cursor-pointer">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 mb-3 text-slate-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="text-sm">No gym package data has been registered yet.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->packages->links() }}
    </div>

    <!-- Dialog Create Package -->
    <livewire:admin.package.create />

    <!-- Dialog Edit Package -->
    <livewire:admin.package.update />

    <!-- Dialog Delete Package -->
    <livewire:admin.package.delete />

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-modal-update-package', () => {
                document.getElementById('modal-update-package').showModal();
            });

            Livewire.on('open-modal-delete-package', () => {
                document.getElementById('modal-delete-package').showModal();
            });

            Livewire.on('success_package', (data) => {
                Swal.fire({
                    title: "Success",
                    text: data.message,
                    icon: "success"
                });
            });

            Livewire.on('error_package', (data) => {
                Swal.fire({
                    title: "Failed",
                    text: data.message,
                    icon: "success"
                });
            });
        });
    </script>
</div>
