<?php
//
//namespace App\Imports;
//
//use App\Models\Product;
//use App\Models\Category;
//use App\Models\Supplier;
//use App\Models\Unit;
//use App\Models\ProductUnit;
//use App\Models\Group;
//use Maatwebsite\Excel\Concerns\ToCollection;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithBatchInserts;
//use Maatwebsite\Excel\Concerns\WithChunkReading;
//use Illuminate\Support\Collection;
//
//class ProductsImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
//{
//    public function collection(Collection $rows)
//    {
//        foreach ($rows as $row) {
//            // 1. Cari atau buat kelompok
//            $group = Group::firstOrCreate(
//                ['code' => $row['kelompok']],
//                ['name' => $row['kelompok']]
//            );
//
//            // 2. Cari atau buat kategori
//            $category = Category::firstOrCreate(
//                ['code' => $row['kategori']],
//                ['name' => $row['kategori']]
//            );
//
//            // 3. Cari atau buat supplier
//            $supplier = Supplier::firstOrCreate(
//                ['code' => $row['supplier']],
//                ['name' => $row['supplier']]
//            );
//
//            // 4. Cari atau buat unit
//            $unit = Unit::firstOrCreate(
//                ['code' => $row['satuan']],
//                ['name' => $row['satuan']]
//            );
//
//            // 5. Buat product
//            $product = Product::create([
//                'code' => $row['kode'],
//                'name' => $row['nama'],
//                'barcode' => $row['barcode'],
//                'group_id' => $group->id,
//                'category_id' => $category->id,
//                'supplier_id' => $supplier->id,
//                'is_active' => true
//            ]);
//
//            // 6. Buat product unit
//            ProductUnit::create([
//                'product_id' => $product->id,
//                'unit_id' => $unit->id,
//                'conversion_factor' => 1,
//                'purchase_price' => $row['hrgbeli'],
//                'selling_price' => $row['hrgjual'],
//                'stock' => $row['stokbwal'],
//                'min_stock' => $row['stokmin'],
//                'is_default' => true
//            ]);
//        }
//    }
//
//    public function rules(): array
//    {
//        return [
//            '*.kode' => 'required|distinct',
//            '*.nama' => 'required',
//            '*.kelompok' => 'required',
//            '*.kategori' => 'required',
//            '*.supplier' => 'required',
//            '*.satuan' => 'required',
//            '*.hrgbeli' => 'required|numeric',
//            '*.hrgjual' => 'required|numeric',
//            '*.stokbwal' => 'required|numeric',
//            '*.stokmin' => 'required|numeric'
//        ];
//    }
//
//    public function batchSize(): int
//    {
//        return 1000;
//    }
//
//    public function chunkSize(): int
//    {
//        return 1000;
//    }
//}
