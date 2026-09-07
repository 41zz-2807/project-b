@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengeluaran" />

    <div class="space-y-6">
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-theme-sm font-medium text-success-700 dark:border-success-800 dark:bg-success-500/10 dark:text-success-500">
                <svg class="shrink-0 fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.7-9.3a1 1 0 0 0-1.4-1.4L9 10.6l-1.3-1.3a1 1 0 1 0-1.4 1.4l2 2a1 1 0 0 0 1.4 0l4-4Z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        @include('pages.transaksi._form', ['type' => 'expense', 'transaction' => $transaction])

        @include('pages.transaksi._recent', [
            'transaksis' => $transaksis,
            'type' => 'expense',
            'title' => 'Pengeluaran',
            'total' => $total,
        ])
    </div>
@endsection