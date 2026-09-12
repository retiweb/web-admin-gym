<?php

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Models\Transaction;

new class extends Component {
    //
    public $transactionId;

    #[On('delete-transaction')]
    public function load($id)
    {
        $this->transactionId = $id;
        $this->dispatch('open-modal-delete-transaction');
    }

    public function deleteTransaction()
    {
        try {
            $transaction = Transaction::destroy($this->transactionId);

            $this->dispatch('close-modal-delete-transaction');
            $this->dispatch('success_transaction_delete');
            $this->dispatch('refresh-transaction-table');
        } catch (\Exception $e) {
            Log::error('error system ' . $e->getMessage());

            $this->dispatch('close-modal-delete-transaction');
            $this->dispatch('failed_transaction_delete');

            return;
        }
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-delete-transaction" @close-modal-delete-transaction.window="$el.close()" x-data={}
        aria-labelledby="dialog-title"
        class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

        <!-- Backdrop Hitam -->
        <div
            class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in">
        </div>

        <div tabindex="0"
            class="fixed inset-0 flex min-h-full items-center justify-center p-4 text-center focus:outline-none sm:p-0 z-10">

            <!-- Panel Modal -->
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                <!-- Body Form (Aksi Submit ke deletePackage) -->
                <form wire:submit="deleteTransaction" id="form-delete-package">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">

                            <!-- Icon Warning Bulat (Warna Merah) -->
                            <div
                                class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>

                            <!-- Teks Konfirmasi -->
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="dialog-title">
                                    Hapus Package
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus transaksi ini?
                                        Data yang sudah dihapus tidak dapat dikembalikan lagi.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer (Buttons) -->
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">

                        <!-- Tombol Hapus (Warna Merah) -->
                        <button type="submit" wire:loading.attr="disabled" wire:target="deletePackage"
                            class="inline-flex w-full justify-center items-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors disabled:opacity-70 disabled:cursor-not-allowed">

                            <!-- Teks "Hapus Data" akan MENGHILANG saat loading -->
                            <span wire:loading.remove wire:target="deletePackage">
                                Hapus Data
                            </span>

                            <!-- Teks "Menghapus..." MUNCUL saat loading -->
                            <span wire:loading wire:target="deletePackage" class="flex items-center gap-2">
                                Menghapus...
                            </span>
                        </button>

                        <!-- Tombol Batal -->
                        <button type="button" @click="document.getElementById('modal-delete-transaction').close()"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </dialog>
</div>
