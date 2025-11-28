@extends('layouts.app')

@section('content')
<div x-data="stockAdditionData()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mt-2 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 md:text-white">
                Tambah Stok Produk
            </h2>
            <p class="text-sm text-gray-700 opacity-90 md:text-purple-100">
                Tambah stok untuk produk yang dipilih
            </p>
        </div>
        <a href="{{ route('stock-additions.index') }}"
           class="px-4 py-2 text-sm font-semibold text-white transition bg-gray-600 rounded-lg shadow-md hover:bg-gray-700">
            <i class="mr-1 fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FORM --}}
    <div class="max-w-3xl p-6 bg-white border border-gray-100 shadow-md rounded-xl">
        <h3 class="mb-6 text-lg font-semibold text-gray-800">Form Tambah Stok</h3>

        @if(session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-200 rounded-lg">
                <i class="mr-2 fa-solid fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-200 rounded-lg">
                <ul class="pl-5 list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('stock-additions.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Product Selection --}}
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Produk <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="product_id"
                            x-model="selectedProduct"
                            @change="updateProductInfo()"
                            required
                            class="w-full px-4 py-3 pr-10 transition border border-gray-300 rounded-lg appearance-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->product_id }}"
                                    data-stock="{{ $product->stock }}"
                                    data-category="{{ $product->category->name ?? '-' }}"
                                    {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                {{ $product->name }} (Stok: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <i class="text-gray-400 fa-solid fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            {{-- Product Info Display --}}
            <div x-show="selectedProduct"
                 x-transition
                 class="p-4 border border-purple-200 bg-purple-50 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Kategori</p>
                        <p class="text-sm font-medium text-gray-800" x-text="productCategory"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Stok Saat Ini</p>
                        <p class="text-sm font-bold text-purple-600" x-text="productStock + ' pcs'"></p>
                    </div>
                </div>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Jumlah Tambahan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number"
                           name="quantity"
                           min="1"
                           value="{{ old('quantity') }}"
                           x-model="quantity"
                           required
                           placeholder="Masukkan jumlah stok yang ditambahkan"
                           class="w-full px-4 py-3 transition border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                        <span class="text-sm text-gray-500">pcs</span>
                    </div>
                </div>
            </div>

            {{-- New Stock Preview --}}
            <div x-show="selectedProduct && quantity > 0"
                 x-transition
                 class="p-4 border border-green-200 bg-green-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Stok Setelah Ditambah</p>
                        <p class="text-2xl font-bold text-green-700" x-text="(parseInt(productStock) + parseInt(quantity)) + ' pcs'"></p>
                    </div>
                    <div class="p-3 text-green-600 rounded-full bg-green-200">
                        <i class="fa-solid fa-arrow-trend-up fa-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block mb-2 text-sm font-semibold text-gray-700">
                    Catatan (Opsional)
                </label>
                <textarea name="notes"
                          rows="3"
                          placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-4 py-3 transition border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('stock-additions.index') }}"
                   class="px-6 py-2.5 text-sm font-semibold text-gray-800 transition bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white transition bg-green-600 rounded-lg shadow-md hover:bg-green-700">
                    <i class="mr-1 fa-solid fa-check"></i> Simpan Penambahan Stok
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function stockAdditionData() {
    return {
        selectedProduct: '{{ old('product_id') }}',
        productStock: 0,
        productCategory: '',
        quantity: {{ old('quantity', 0) }},

        init() {
            if (this.selectedProduct) {
                this.updateProductInfo();
            }
        },

        updateProductInfo() {
            const select = document.querySelector('select[name="product_id"]');
            const option = select.options[select.selectedIndex];

            if (option.value) {
                this.productStock = option.dataset.stock || 0;
                this.productCategory = option.dataset.category || '-';
            } else {
                this.productStock = 0;
                this.productCategory = '';
            }
        }
    }
}
</script>
@endsection
