@extends('layouts.app')

@section('content')
<div x-data="{ showDeleteModal: false, deleteId: null }">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mt-2 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 md:text-white">
                Riwayat Penambahan Stok
            </h2>
            <p class="text-sm text-gray-700 opacity-90 md:text-purple-100">
                Kelola dan lihat riwayat penambahan stok produk
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="p-6 bg-white border border-gray-100 shadow-md rounded-xl ">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Daftar Penambahan Stok</h3>

        {{-- Search + Add --}}
        <div class="flex flex-col items-start justify-between w-full gap-3 mb-6 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('stock-additions.index') }}" class="flex w-full gap-3 sm:w-auto">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari produk atau user..."
                    class="w-full px-4 py-2 transition border border-gray-300 rounded-lg sm:w-64 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">

                <button type="submit"
                    class="px-4 py-2 font-semibold text-white transition bg-purple-600 rounded-lg shadow-md hover:bg-purple-700 hover:shadow-lg">
                    <i class="mr-1 fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>

            <a href="{{ route('stock-additions.create') }}"
                class="px-4 py-2 font-semibold text-white transition bg-green-600 rounded-lg shadow-md hover:bg-green-700">
                <i class="mr-1 fa-solid fa-circle-plus"></i> Tambah Stok
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-200 rounded-lg">
                <i class="mr-2 fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-200 rounded-lg">
                <i class="mr-2 fa-solid fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- TABLE --}}
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead>
                    <tr class="text-xs tracking-wider text-gray-600 uppercase bg-gray-50">
                        <th class="px-4 py-3 font-bold text-left">ID</th>
                        <th class="px-4 py-3 font-bold text-left">Tanggal</th>
                        <th class="px-4 py-3 font-bold text-left">Produk</th>
                        <th class="px-4 py-3 font-bold text-left">Kategori</th>
                        <th class="px-4 py-3 font-bold text-left">Jumlah</th>
                        <th class="px-4 py-3 font-bold text-left">User</th>
                        <th class="px-4 py-3 font-bold text-left">Catatan</th>
                        <th class="px-4 py-3 font-bold text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($stockAdditions as $addition)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-gray-700">
                            #{{ str_pad($addition->stock_addition_id, 4, '0', STR_PAD_LEFT) }}
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $addition->added_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $addition->product->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $addition->product->category->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-sm font-bold text-green-700 bg-green-100 rounded-full">
                                +{{ number_format($addition->quantity) }} pcs
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $addition->user->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $addition->notes ?? '-' }}
                        </td>

                        <td class="flex justify-center gap-3 px-4 py-3">
                            <button
                                @click="showDeleteModal = true; deleteId = {{ $addition->stock_addition_id }}"
                                class="text-red-500 transition hover:text-red-700"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 italic text-center text-gray-500">
                            Belum ada data penambahan stok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-5">
            {{ $stockAdditions->links('components.pagination.custom') }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div
        x-show="showDeleteModal"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
    >
        <div
            x-show="showDeleteModal"
            x-transition.scale.origin.bottom.duration.300ms
            class="w-full max-w-md bg-white shadow-2xl rounded-2xl"
            @click.away="showDeleteModal = false"
        >
            <div class="px-6 py-4 border-b bg-gradient-to-r from-red-600 to-red-500">
                <h2 class="text-xl font-semibold text-white">Konfirmasi Hapus</h2>
            </div>

            <div class="px-6 py-5">
                <p class="text-gray-700">Apakah Anda yakin ingin menghapus data penambahan stok ini?</p>
                <p class="mt-2 text-sm text-red-600">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    Stok produk akan dikurangi sesuai jumlah yang ditambahkan.
                </p>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50">
                 <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-semibold text-gray-800 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                    Batal
                </button>
                <form :action="`{{ url('stock-additions') }}/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white transition bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
