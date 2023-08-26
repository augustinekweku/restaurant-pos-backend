<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditor_order extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'company_name',
        'order_total',
        'order_number',
        'paid',
        'balance',
        'invoice_number',
        'notes',
        'status',
        'user_id',
        'due_date',
    ];

    public function creditor_order_details()
    {
        return $this->hasMany(Creditor_order_details::class, 'creditor_order_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
