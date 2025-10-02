<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    protected $table = 'forms';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'form_fields',
        'is_public',
        'slug',
        'header_image',
        'theme_color',
        'background_color',
        'font_family',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(FormResponse::class);
    }
}
