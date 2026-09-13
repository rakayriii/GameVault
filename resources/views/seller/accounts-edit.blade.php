@extends('layouts.app')

@section('title', 'Edit Listing · Seller Center')

@section('content')
    <div class="max-w-[900px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-secondary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="{{ route('seller.accounts') }}" class="hover:text-secondary transition-colors">Listing</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Edit Listing</span>
        </nav>

        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Edit Listing</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Perubahan harga / judul pada listing live langsung diterapkan. Screenshot akun juga bisa kamu kelola di sini.</p>
        </div>

        @include('seller.partials.account-form', ['account' => $account, 'games' => $games])
    </div>
@endsection