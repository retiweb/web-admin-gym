<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

new class extends Component {
    //
    public $name = '';
    public $price = 0;
    public $duration = 1;
    public $is_active = 1;

    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function savePackage()
    {
       $rules = [
            'name'      => 'required|string|min:5|max:100|unique:packages,name',
            'price'     => 'required|integer|min:0|max:100000000',
            'duration'  => 'required|integer|min:1|max:3650',
            'is_active' => 'boolean',
        ];

        $messages = [
            'name.required'   => 'The package name is required.',
            'name.string'     => 'The package name must be valid text.',
            'name.min'        => 'The package name must be at least 5 characters.',
            'name.max'        => 'The package name cannot exceed 100 characters.',
            'name.unique'     => 'This package name is already taken.',

            'price.required'  => 'The price is required.',
            'price.integer'   => 'The price must be a valid number.',
            'price.min'       => 'The price cannot be less than 0.',
            'price.max'       => 'The price cannot exceed 100,000,000.',

            'duration.required' => 'The duration is required.',
            'duration.integer'  => 'The duration must be a valid number.',
            'duration.min'      => 'The duration must be at least 1 day.',
            'duration.max'      => 'The duration cannot exceed 3,650 days.',

            'is_active.boolean' => 'The status format is invalid.',
        ];

        $this->validate($rules, $messages);

        try {
            Package::create([
                'name' => $this->name,
                'duration_days' => $this->duration,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);

            $this->reset();

           $this->dispatch('success_package', message: 'New package created successfully!');

            $this->dispatch('close-modal-create-package');

            $this->dispatch('refresh-package-table');
        } catch (\Exception $e) {
           Log::error('Error create package: ' . $e->getMessage());

            session()->flash('error_create_package', $e->getMessage());

            return;
        }
    }
};
?>

<dialog wire:ignore.self @close="$wire.resetForm()"  @close-modal-create-package.window="$el.close()" id="modal-create-package" aria-labelledby="dialog-title" x-data={}
    class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

    <div
        class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in">
    </div>

    <div tabindex="0"
        class="fixed inset-0 flex min-h-full items-center justify-center p-4 text-center focus:outline-none sm:p-0 z-10">

        <div
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50">
                        <!-- Icon Package/Tag -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="dialog-title" class="text-lg font-semibold text-gray-900">Create New Package</h3>
                        <p class="text-sm text-gray-500">Input package information</p>
                    </div>
                </div>
            </div>

             @if (session()->has('error_create_package'))
                <div
                    class="mb-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span class="font-medium">{{ session('error_package') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'"
                        class="text-red-600 hover:text-red-800 font-bold px-2">
                        &times;
                    </button>
                </div>
            @endif

            <form wire:submit="savePackage" id="form-package">
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Package
                            Name</label>
                        <input wire:model="name" type="text" id="name" name="name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                            placeholder="e.g. Paket Bulanan Regular" required />

                        <div class="text-red-600 text-sm mt-1">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Price (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <span class="text-gray-500 font-medium sm:text-sm">Rp</span>
                            </div>
                            <input wire:model="price" type="number" id="price" name="price" min="0"
                                class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                                placeholder="250000" required />
                        </div>
                        <div class="text-red-600 text-sm mt-1">
                            @error('price')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1.5">Duration
                            (Days)</label>
                        <div class="relative">
                            <input wire:model="duration" type="number" id="duration" name="duration" min="1"
                                class="w-full pl-4 pr-16 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                                placeholder="30" required />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Hari</span>
                            </div>
                        </div>
                        <div class="text-red-600 text-sm mt-1">
                            @error('duration')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select wire:model="is_active" id="status" name="is_active"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select Status</option>
                            <option value="1">Aktif (Active)</option>
                            <option value="0">Tidak Aktif (Inactive)</option>
                        </select>
                        <div class="text-red-600 text-sm mt-1">
                            @error('status')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                    <button type="submit"
                        class="inline-flex w-full sm:w-auto justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <span class="in-data-loading:hidden">Save</span>
                        <span class="not-in-data-loading:hidden">Saving...</span>
                    </button>
                    <!-- Perbaikan: Arahkan ID Batal ke modal-tambah-package -->
                    <button type="button"
                        @click="$wire.resetForm(); document.getElementById('modal-create-package').close()"
                        class="inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                        <span class="data-loading:opacity-50">Cancel</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</dialog>
