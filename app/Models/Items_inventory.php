<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items_inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'old_qty',
        'old_stock',
        'new_qty_left',
        'qty_added',
        'new_stock',
        'date',
        'item_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
