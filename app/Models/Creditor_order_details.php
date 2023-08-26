<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditor_order_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_name',
        'creditor_order_id',
        'price',
        'quantity',
        'amount'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function creditor_order()
    {
        return $this->belongsTo(Creditor_order::class);
    }
}
