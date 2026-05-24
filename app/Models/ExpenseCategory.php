<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['name'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category', 'id');
    }
}

