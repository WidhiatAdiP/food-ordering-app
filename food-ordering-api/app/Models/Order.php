<?php

namespace App\Models;

use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', // Nama Customer
        'table_no', // Nomor Meja
        'order_date', // Tanggal Order
        'order_time', // Waktu Order
        'status', // Status Order
        'total', // Total Order
        'waitress_id' // Id Pelayan
    ];

    public function sumOrderPrice() // Fungsi untuk menghitung total harga dari semua order
    {
        $orderDetail = OrderDetail::where('order_id', $this->id) // Mengambil detail pesanan berdasarkan order_id
        ->select('price', 'qty') // Memilih hanya kolom 'price' dan 'qty'
        ->get(); // Mengambil semua hasil
        $totalPerItem = collect($orderDetail)->map(function($item) { // Menghitung total harga per item berdasarkan price dan qty
            return $item->price * $item->qty;
        });

        $sum = collect($totalPerItem)->sum(); // Menghitung total dari semua total per item

        return $sum; // Mengembalikan total harga seluruh order
    }

    /**
     * Get all of the orderDetail for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail(): HasMany // Fungsi untuk mengambil semua detail pesanan yang berhubungan dengan order
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function waitress(): BelongsTo // Fungsi untuk mengambil user yang memiliki peran sebagai pelayan
    {
        return $this->belongsTo(User::class, 'waitress_id', 'id');
    }
    public function cashier(): BelongsTo // Fungsi untuk mengambil user yang memiliki peran sebagai kasir
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
