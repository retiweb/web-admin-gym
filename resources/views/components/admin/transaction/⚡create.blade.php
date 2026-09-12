<?php

use Livewire\Component;
use App\Models\Member;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

new class extends Component {
    //
    public $is_member = '1';
    public $member_id;
    public $guest_name;
    public $package_id;
    public $payment_method = 'Cash';

    public function getMembersProperty()
    {
        return Member::select('id', 'name')->orderBy('name', 'asc')->get();
    }

    public function getPackagesProperty()
    {
        return Package::select('id', 'name', 'price')->orderBy('name', 'asc')->get();
    }

    public function saveTransaction()
    {
        $this->validate(
            [
                'is_member' => 'required',
                'member_id' => 'required_if:is_member,1',
                'guest_name' => 'required_if:is_member,0|max:100',
                'package_id' => 'required_if:is_member,1|nullable|exists:packages,id',
                'payment_method' => 'required|string',
            ],
            [
                'member_id.required_if' => 'Silakan pilih data member terlebih dahulu.',
                'guest_name.required_if' => 'Nama tamu wajib diisi jika pelanggan bukan member.',
                'package_id.required' => 'Anda belum memilih paket gym.',
            ],
        );

        try {
            DB::transaction(function () {
                if ($this->package_id) {
                    $package = Package::findOrFail($this->package_id);
                }else{
                    $package = null;
                }

                $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

                $transaction = Transaction::create([
                    'invoice_no' => $invoiceNumber,
                    'package_id' => $package->id ?? null,
                    'payment_method' => $this->payment_method,
                    'total_payment' => $package->price ?? 15000,
                    'active_from' => now(),
                    'active_until' => $this->is_member == '1' ? now()->addDays($package->duration_days) : now(),
                    'member_id' => $this->is_member == '1' ? $this->member_id : null,
                    'guest_name' => $this->is_member == '0' ? $this->guest_name : null,
                ]);

                if ($this->is_member == '1') {
                    Member::where('id', $this->member_id)->update([
                        'expired_at' => $transaction->active_until,
                    ]);
                }
            });

            $this->reset(['is_member', 'member_id', 'guest_name', 'package_id', 'payment_method']);
            $this->payment_method = 'Cash';
            $this->dispatch('close-modal-tambah-transaction');

            $this->dispatch('success_transaction_create', message: 'Transaksi berhasil dicatat!');
            $this->dispatch('refresh-transaction-table');
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan transaksi ' . $e->getMessage());

            session()->flash('failed-create-transaction', 'Terjadi kesalahan sistem. Gagal menambahkan data.');

            return;
        }
    }
};
?>

<div>
    <dialog wire:ignore.self id="modal-tambah-transaction" @close-modal-tambah-transaction.window="$el.close()" x-data={}
        class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">

        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4 z-10">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Catat Transaksi</h2>
                    <p class="text-sm text-slate-500 mt-1">Pilih tipe pelanggan dan paket gym.</p>
                </div>

                @if (session()->has('failed-create-transaction'))
                    <div class="mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                        {{ session('failed-create-transaction') }}
                    </div>
                @endif

                <form wire:submit="saveTransaction" class="space-y-5">

                    <div class="flex p-1 bg-slate-100 rounded-lg">
                        <label class="flex-1 text-center">
                            <input type="radio" wire:model.live="is_member" value="1" class="peer sr-only">
                            <div
                                class="py-2 text-sm font-medium rounded-md cursor-pointer peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500 transition-all">
                                Member
                            </div>
                        </label>
                        <label class="flex-1 text-center">
                            <input type="radio" wire:model.live="is_member" value="0" class="peer sr-only">
                            <div
                                class="py-2 text-sm font-medium rounded-md cursor-pointer peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500 transition-all">
                                Non-Member
                            </div>
                        </label>
                    </div>


                    <div class="min-h-17.5">
                        @if ($is_member == '1')
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Member <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="member_id"
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none">
                                    <option value="">-- Pilih Member Terdaftar --</option>
                                    @foreach ($this->members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                                <!-- Tampilkan pesan error di sini -->
                                @error('member_id')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Paket Gym <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="package_id"
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none">
                                    <option value="">-- Pilih Paket --</option>
                                    @foreach ($this->packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }} - Rp
                                            {{ number_format($package->price, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                @error('package_id')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Tamu <span
                                        class="text-red-500">*</span></label>
                                <input wire:model="guest_name" type="text" placeholder="Masukkan nama pelanggan..."
                                    class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none">
                                <!-- Tampilkan pesan error di sini -->
                                @error('guest_name')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode Pembayaran</label>
                        <select wire:model="payment_method"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none">
                            <option value="Cash">Cash (Tunai)</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS / E-Wallet</option>
                        </select>
                        @error('payment_method')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="document.getElementById('modal-tambah-transaction').close()"
                            class="flex-1 rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 flex justify-center items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors disabled:opacity-70 disabled:cursor-not-allowed">


                            <span wire:loading.remove wire:target="saveTransaction">Simpan Transaksi</span>


                            <span wire:loading wire:target="saveTransaction" class="flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </dialog>
</div>
