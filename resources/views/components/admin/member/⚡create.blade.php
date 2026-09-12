<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Member;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithFileUploads;

    public $name = '';
    public $gender = '';
    public $phone = '';
    public $package = '';
    public $payment_method = '';
    public $photo;

    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function getPackagesProperty()
    {
        return Package::orderBy('created_at', 'asc')->get();
    }

    public function generateMemberCode()
    {
        $yearMonth = now()->format('ym');
        $prefix = "GYM-$yearMonth";

        $lastMember = Member::where('member_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastMember) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastMember->member_code, -4);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function saveMember()
    {

        $rules = [
            'name'           => 'required|string|min:3|max:150',
            'gender'         => 'required|in:Male,Female',
            'phone'          => 'required|numeric|digits_between:10,15|unique:members,phone',
            'package'        => 'required|exists:packages,id',
            'payment_method' => 'required|string|in:Cash,Transfer',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ];

        $messages = [
            'name.required'             => 'The full name is required.',
            'name.string'               => 'The full name must be valid text.',
            'name.min'                  => 'The full name must be at least 3 characters.',
            'name.max'                  => 'The full name cannot exceed 150 characters.',
            'gender.required'           => 'Please select a gender.',
            'gender.in'                 => 'The selected gender is invalid.',
            'phone.required'            => 'The phone number is required.',
            'phone.numeric'             => 'The phone number must contain only numbers.',
            'phone.digits_between'      => 'The phone number must be between 10 and 15 digits.',
            'phone.unique'              => 'This phone number is already registered.',
            'package.required'          => 'Please select a package.',
            'package.exists'            => 'The selected package is invalid.',
            'payment_method.required'   => 'Please select a payment method.',
            'payment_method.in'         => 'The selected payment method is invalid.',
            'photo.image'               => 'The uploaded file must be an image.',
            'photo.mimes'               => 'The photo format must be JPG, JPEG, or PNG.',
            'photo.max'                 => 'The photo size cannot exceed 1 MB.',
        ];

        $this->validate($rules, $messages);

        try {
            DB::transaction(function () {
                $photoPath = null;
                $package = Package::findOrFail($this->package);
                $expiredDate = Carbon::now()->addDays($package->duration_days);

                if ($this->photo) {
                    $photoPath = $this->photo->store('photos', 'public');
                }

                $member = Member::create([
                    'member_code' => $this->generateMemberCode(),
                    'name' => $this->name,
                    'phone' => $this->phone,
                    'gender' => $this->gender,
                    'expired_at' => $expiredDate,
                    'photo' => $photoPath,
                ]);

                $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

                Transaction::create([
                    'invoice_no' => $invoiceNumber,
                    'member_id' => $member->id,
                    'package_id' => $package->id,
                    'payment_method' => $this->payment_method,
                    'total_payment' => $package->price,
                    'active_from' => Carbon::now(),
                    'active_until' => $expiredDate,
                ]);
            });

            $this->reset();
            $this->dispatch('success_member', message: 'Member created successfully.');
            $this->dispatch('close-modal-create-member');
            $this->dispatch('refresh-member-table');
        } catch (\Exception $e) {
            Log::error('Failed to save gym member data: ' . $e->getMessage());

            session()->flash('fail_create_member', $e->getMessage());
        }
    }
};
?>

<dialog wire:ignore.self @close="$wire.resetForm()" @close-modal-create-member.window="$el.close()" x-data="{}"
    id="modal-create-member" aria-labelledby="dialog-title"
    class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
    <div
        class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in">
    </div>

    <div tabindex="0"
        class="fixed inset-0 flex min-h-full items-center justify-center p-4 text-center focus:outline-none sm:p-0 z-10">
        <!-- Panel Modal -->
        <div
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="dialog-title" class="text-lg font-semibold text-gray-900">Add New Member</h3>
                        <p class="text-sm text-gray-500">Enter the registration details.</p>
                    </div>
                </div>
            </div>

            @if (session()->has('fail_create_member'))
                <div
                    class="m-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span class="font-medium">{{ session('fail_create_member') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.style.display='none'"
                        class="text-red-600 hover:text-red-800 font-bold px-2">
                        &times;
                    </button>
                </div>
            @endif

            <form wire:submit="saveMember" id="form-member">
                <div class="px-6 py-5 space-y-4">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
                        <input wire:model="name" type="text" id="name" name="name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                            placeholder="John Doe" required />

                        <div class="text-red-600">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number
                            (WhatsApp)</label>
                        <input wire:model="phone" type="tel" id="phone" name="phone"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm"
                            placeholder="08123456789" required />
                        <div class="text-red-600">
                            @error('phone')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                        <select wire:model="gender" id="gender" name="gender"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <div class="text-red-600">
                            @error('gender')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="package" class="block text-sm font-medium text-gray-700 mb-1.5">Package</label>
                        <select wire:model="package" id="package" name="package"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select Package</option>
                            @foreach ($this->packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-red-600">
                            @error('package')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1.5">Payment
                            Method</label>
                        <select wire:model="payment_method" id="payment_method" name="payment_method"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200 text-gray-900 shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                        <div class="text-red-600">
                            @error('payment_method')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-1.5">Profile Photo
                            (Optional)</label>
                        <input wire:model="photo" type="file" id="photo" name="photo"
                            accept="image/png, image/jpeg"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-600
                            file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium
                            file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer shadow-sm" />
                        <div class="text-red-600">
                            @error('photo')
                                {{ $message }}
                            @enderror
                        </div>

                        @if ($photo)
                            <img class="max-w-40 max-h-40 p-4" src="{{ $photo->temporaryUrl() }}">
                        @endif
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                    <button type="submit"
                        class="inline-flex w-full sm:w-auto justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                        <span class="in-data-loading:hidden">Save</span>
                        <span class="not-in-data-loading:hidden">Saving...</span>
                    </button>
                    <button type="button"
                        @click="$wire.resetForm(); document.getElementById('modal-create-member').close()"
                        class="inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">
                        <span class="data-loading:opacity-50">Cancel</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</dialog>
