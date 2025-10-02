<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormResponse extends Model
{
    protected $table = 'form_responses';
    use HasFactory;

    protected $fillable = ['form_id','response_data'];

    protected $casts = [
        'response_data' => 'array',
    ];
}
