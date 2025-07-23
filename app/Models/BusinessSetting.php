<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'phone_number',
        'address',
        'access_information',
        'business_hours',
        'website_url',
        'site_name',
        'shop_description',
        'top_image_path',
        'site_public',
        'reply_email',
        'terms_of_use',
        'privacy_policy',
        'google_analytics_id',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'site_public' => 'boolean',
    ];
     public function getPathTopImageUrlAttribute() 
    {
        if (filter_var($this->top_image_path, FILTER_VALIDATE_URL)) {
            return $this->top_image_path;
        }
        
        return Storage::disk('s3')->url($this->top_image_path);
    }
}
