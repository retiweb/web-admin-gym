<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

new class extends Component {
    public $packageId;
    public $name;
    public $price;
    public $duration;
    public $is_active;

    #[On('edit-package')]
    public function loadPackageData($id)
    {
        try {
            $this->packageId = $id;
            $package = Package::findOrFail($this->packageId);

            $this->name = $package->name;
            $this->price = $package->price;
            $this->duration = $package->duration_days;
            $this->is_active = $package->is_active ? 1 : 0;

            $this->resetValidation();
            $this->dispatch('open-modal-update-package');

        } catch (\Exception $e) {
            Log::error('Failed to load package data for editing: ' . $e->getMessage());
            session()->flash('error_package', 'System error. Failed to retrieve package data.');
        }
    }

    public function updatePackage()
    {
        $rules = [
            'name' => 'required|string|min:3|max:100|unique:packages,name,' . $this->packageId,
            'price' => 'required|integer|min:0|max:100000000',
            'duration' => 'required|integer|min:1|max:3650',
            'is_active' => 'required|boolean',
        ];

        $messages = [
            'name.required' => 'The package name field is required.',
            'name.min' => 'The package name must be at least 3 characters.',
            'name.unique' => 'This package name has already been taken.',
            'price.required' => 'The price field is required.',
            'price.integer' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'duration.required' => 'The duration field is required.',
            'duration.min' => 'The duration must be at least 1 day.',
            'is_active.required' => 'The status field is required.',
        ];

        $this->validate($rules, $messages);

        try {
            $package = Package::findOrFail($this->packageId);

            $package->update([
                'name' => $this->name,
                'duration_days' => $this->duration,
                'price' => $this->price,
                'is_active' => $this->is_active,
            ]);


            $this->dispatch('close-modal-update-package');
            $this->dispatch('success_update_package', message: 'Package updated successfully!');
            $this->dispatch('refresh-package-table');

        } catch (\Exception $e) {
            Log::error('Failed to update package ID ' . $this->packageId . ': ' . $e->getMessage());
            session()->flash('error_update_package', $e->getMessage());
        }
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-update-package" @close-modal-update-package.window="$el.close()" aria-labelledby="dialog-title" x-data={}
        class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

        <div
            class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in">
        </div>

        <div tabindex="0"
            class="fixed inset-0 flex min-h-full items-center justify-center p-4 text-center focus:outline-none sm:p-0 z-10">

            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                @if (session()->has('error_update_package'))
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

                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="dialog-title" class="text-lg font-semibold text-gray-900">Update Package</h3>
                            <p class="text-sm text-gray-500">Edit package information</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="updatePackage" id="form-edit-package">
                    <div class="px-6 py-5 space-y-4">

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Package Name</label>
                            <input wire:model="name" type="text" id="name" name="name"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                                placeholder="e.g. Regular Monthly Package" required />

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
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-1.5">Duration (Days)</label>
                            <div class="relative">
                                <input wire:model="duration" type="number" id="duration" name="duration"
                                    min="1"
                                    class="w-full pl-4 pr-16 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                                    placeholder="30" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Days</span>
                                </div>
                            </div>
                            <div class="text-red-600 text-sm mt-1">
                                @error('duration')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                            <select wire:model="is_active" id="is_active" name="is_active"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm cursor-pointer">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="text-red-600 text-sm mt-1">
                                @error('is_active')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                        <button type="submit" wire:loading.attr="disabled" wire:target="updatePackage"
                            class="inline-flex w-full sm:w-auto justify-center items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors disabled:opacity-70 disabled:cursor-not-allowed">

                            <span wire:loading.remove wire:target="updatePackage">
                                Update Data
                            </span>

                            <span wire:loading wire:target="updatePackage" class="flex items-center gap-2">
                                Saving...
                            </span>
                        </button>

                        <button type="button" @click="document.getElementById('modal-update-package').close()"
                            class="inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                            <span>Cancel</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </dialog>
</div>
