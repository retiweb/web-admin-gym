<?php

use Livewire\Component;
use App\Models\Member;
use Livewire\Attributes\On;

new class extends Component {
    //
    #[Session]
    public $search = '';

    #[On('refresh-member-table')]
    public function refreshTable() {}

    public function getMembersProperty()
    {
        return Member::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%' . $this->search . '%')->orWhere('member_code', 'ilike', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return $this->view()->title('Member Page');
    }
};
?>


<div class="w-full">

    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-4 sm:p-5 rounded-xl border border-gray-100 shadow-sm">

        <div>
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">List Member</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Manage and track all gym members active subscriptions.</p>
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search members..."
                    class="w-full ps-10 pe-4 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all duration-200 text-gray-900 shadow-sm" />
            </div>

            <button command="show-modal" commandfor="modal-create-member" type="button"
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

        <table class="w-full text-left text-sm text-slate-100">
            <thead class="bg-slate-50/50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 font-medium whitespace-nowrap">Member Code</th>
                    <th class="px-6 py-3 font-medium">Photo</th>
                    <th class="px-6 py-3 font-medium">Name</th>
                    <th class="px-6 py-3 font-medium">Gender</th>
                    <th class="px-6 py-3 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y border-gray-200">

                @forelse ($this->members as $member)
                    <tr wire:key={{ $member->id }} class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $member->member_code }}</td>
                        <td class="px-6 py-4">
                            @if ($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="Photo of {{ $member->name }}"
                                    class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-200">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0D8ABC&color=fff"
                                    alt="Default Avatar"
                                    class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-200">
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $member->gender }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="$dispatch('edit-member', {id: {{ $member->id }}})"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition-colors cursor-pointer">Edit</button>
                                <button wire:click="$dispatch('delete-member', {id: {{ $member->id }}})"
                                    class="text-red-600 hover:text-red-800 font-medium transition-colors cursor-pointer">Hapus</button>
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
                                <span class="text-sm">No gym member data has been registered yet.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $this->members->links() }}
        </div>
    </div>

    <!-- Dialog Create Member -->
    <livewire:admin.member.create />

    <!-- Dialog Update Member -->
    <livewire:admin.member.update />

    <!-- Dialog Delete Member -->
    <livewire:admin.member.delete />

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-modal-update-member', () => {
                document.getElementById('modal-update-member').showModal();
            });

            Livewire.on('open-modal-delete-member', () => {
                document.getElementById('modal-delete-member').showModal();
            });

            Livewire.on('success_member', (data) => {
                Swal.fire({
                    title: "Success",
                    text: data.message,
                    icon: "success"
                });
            });


            Livewire.on('failed_member', (data) => {
                Swal.fire({
                    title: "Failed",
                    text: data.message,
                    icon: "error"
                });
            });
        })
    </script>
</div>
