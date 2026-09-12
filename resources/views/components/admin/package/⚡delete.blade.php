<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Package;
use Illuminate\Support\Facades\Log;

new class extends Component {
    public $packageId;
    public $name;

    #[On('delete-package')]
    public function load($id)
    {
        try {
            $this->packageId = $id;
            $package = Package::findOrFail($id);
            $this->name = $package->name;

            $this->resetValidation();
            $this->dispatch('open-modal-delete-package');
        } catch (\Exception $e) {
            Log::error('Failed to load package data for deletion: ' . $e->getMessage());
            session()->flash('error_package', 'System error. Failed to retrieve package data.');
        }
    }

    public function deletePackage()
    {
        try {
            Package::destroy($this->packageId);

            $this->dispatch('success_package', message:'Package deleted successfully!');

            $this->dispatch('close-modal-delete-package');

            $this->dispatch('refresh-package-table');
        } catch (\Exception $e) {
            Log::error('Failed to delete package ID ' . $this->packageId . ': ' . $e->getMessage());

           $this->dispatch('error_package', message: $e->getMessage());

            $this->dispatch('close-modal-delete-package');
            return;
        }
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-delete-package" @close-modal-delete-package.window="$el.close()" aria-labelledby="dialog-title" x-data={}
        class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

        <!-- Backdrop Hitam -->
        <div
            class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in">
        </div>

        <div tabindex="0"
            class="fixed inset-0 flex min-h-full items-center justify-center p-4 text-center focus:outline-none sm:p-0 z-10">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                <form wire:submit="deletePackage" id="form-delete-package">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="dialog-title">
                                    Delete Package
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete the package <span
                                            class="font-bold text-gray-800">"{{ $name }}"</span>?
                                        This action cannot be undone.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit" wire:loading.attr="disabled" wire:target="deletePackage"
                            class="inline-flex w-full justify-center items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="deletePackage">
                                Delete Data
                            </span>
                            <span wire:loading wire:target="deletePackage" class="flex items-center gap-2">
                                Deleting...
                            </span>
                        </button>

                        <button type="button" @click="document.getElementById('modal-delete-package').close()"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Cancel
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </dialog>
</div>
