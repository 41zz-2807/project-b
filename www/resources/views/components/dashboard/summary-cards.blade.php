@props([
    'totalPemasukan' => 0,
    'totalPengeluaran' => 0,
    'totalSaldo' => 0,
])

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-3">
    {{-- Total Pemasukan --}}
    <div
        class="rounded-2xl border border-gray-200 border-s-4 border-s-success-500 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] dark:border-s-success-500 md:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500">
                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12 3.75a.75.75 0 0 1 .75.75v9.19l2.47-2.47a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 1 1 1.06-1.06l2.47 2.47V4.5a.75.75 0 0 1 .75-.75ZM4.5 16.5a.75.75 0 0 0-1.5 0v2.25A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25V16.5a.75.75 0 0 0-1.5 0v2.25a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V16.5Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
                    <h4 class="mt-2 truncate text-xl font-bold text-success-600 dark:text-success-500 sm:text-2xl">
                        {{ rupiah($totalPemasukan) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Pengeluaran --}}
    <div
        class="rounded-2xl border border-gray-200 border-s-4 border-s-error-500 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] dark:border-s-error-500 md:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-500">
                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12 3.75a.75.75 0 0 1 .75.75v9.19l2.47-2.47a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 1 1 1.06-1.06l2.47 2.47V4.5a.75.75 0 0 1 .75-.75ZM4.5 16.5a.75.75 0 0 0-1.5 0v2.25A2.25 2.25 0 0 0 5.25 21h13.5a2.25 2.25 0 0 0 2.25-2.25V16.5a.75.75 0 0 0-1.5 0v2.25a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V16.5Z"
                            transform="rotate(180 12 12)" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
                    <h4 class="mt-2 truncate text-xl font-bold text-error-600 dark:text-error-500 sm:text-2xl">
                        {{ rupiah($totalPengeluaran) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Saldo --}}
    <div
        class="rounded-2xl border border-gray-200 border-s-4 border-s-brand-500 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03] dark:border-s-brand-500 sm:col-span-2 md:p-6 xl:col-span-1">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-500">
                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M4.5 5.25A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75h15a2.25 2.25 0 0 0 2.25-2.25v-9A2.25 2.25 0 0 0 19.5 5.25h-15Zm.75 3.75h13.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 1 .75-.75Zm5.25 2.25a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo</p>
                    <h4 class="mt-2 truncate text-xl font-bold text-brand-600 dark:text-brand-500 sm:text-2xl">
                        {{ rupiah($totalSaldo) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>