<?php

use Livewire\Component;
use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $totalMembers;
    public $totalTransactions;
    public $revenueThisMonth;

    public function mount()
    {
        $now = Carbon::now();

        $this->totalMembers = Member::count();
        $this->totalTransactions = Transaction::count();
        $this->revenueThisMonth = Transaction::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total_payment');
    }

    public function getRevenueChartProperty()
    {
        $transactions = Transaction::select(DB::raw("TO_CHAR(created_at, 'YYYY-MM') as year_month"), DB::raw("TO_CHAR(created_at, 'Mon') as month_name"), DB::raw('SUM(total_payment) as total'))
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"), DB::raw("TO_CHAR(created_at, 'Mon')"))
            ->orderBy('year_month', 'asc')
            ->get();

        $categories = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');

            $monthName = $date->format('M'); // Jan, Feb, Mar, etc.

            $match = $transactions->firstWhere('year_month', $monthKey);

            $categories[] = $monthName;
            $data[] = $match ? (int) $match->total : 0;
        }

        return [
            'categories' => $categories,
            'data' => $data,
        ];
    }

    public function getLastMembersProperty()
    {
        return Member::select('id', 'name', 'member_code', 'created_at')->orderBy('created_at', 'desc')->limit(5)->get();
    }

    public function render()
    {
        return $this->view()->title('Dashboard');
    }
};
?>

<div class="flex flex-col flex-1 w-full">
    <!-- Grid Wrapper for 3 Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <div
            class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 duration-300">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Members</p>
                <h5 class="text-2xl font-bold text-slate-800">{{ number_format($this->totalMembers, 0, ',', '.') }}</h5>
            </div>
        </div>

        <div
            class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 duration-300">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Transactions</p>
                <h5 class="text-2xl font-bold text-slate-800">{{ number_format($this->totalTransactions, 0, ',', '.') }}
                </h5>
            </div>
        </div>

        <div
            class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex items-center gap-5 transition-transform hover:-translate-y-1 duration-300">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Revenue This Month</p>
                <h5 class="text-2xl font-bold text-slate-800">Rp
                    {{ number_format($this->revenueThisMonth, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>

    <!-- Chart & Side Panel Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <div class="col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm" x-data="revenueChart(@js($this->revenueChart))">
            <div class="mb-4">
                <h4 class="text-lg font-semibold text-slate-800">Monthly Revenue Statistics</h4>
                <p class="text-sm text-slate-500">Monthly revenue growth trend.</p>
            </div>
            <div x-ref="chartContainer" class="w-full"></div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="mb-4">
                <h4 class="text-lg font-semibold text-slate-800">Recent Members</h4>
                <p class="text-sm text-slate-500">Latest registered gym members.</p>
            </div>

            <div class="space-y-4">
                @forelse ($this->lastMembers as $member)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $member->name }}</p>
                            <p class="text-xs text-slate-400">Joined {{ $member->created_at->diffForHumans() }}</p>
                        </div>
                        <span
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            {{ $member->member_code }}
                        </span>
                    </div>
                @empty
                    <div class="text-sm text-slate-400 text-center py-2">
                        No members registered yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@if (session()->has('success_login'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "Success",
                text: "{{ session('success_login') }}",
                icon: "success",
                timer: 2500, // Otomatis hilang dalam 2.5 detik
                showConfirmButton: false
            });
        });
    </script>
@endif

@push('scripts')
    <script>
        window.revenueChart = function(chartData) {
            return {
                init() {
                    let options = {
                        chart: {
                            type: 'area',
                            height: 300,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'inherit'
                        },
                        series: [{
                            name: 'Revenue',
                            data: chartData.data
                        }],
                        xaxis: {
                            categories: chartData.categories
                        },
                        colors: ['#4f46e5'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.05,
                                stops: [0, 100]
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    };

                    let chart = new ApexCharts(this.$refs.chartContainer, options);
                    chart.render();
                }
            }
        }
    </script>
@endpush
