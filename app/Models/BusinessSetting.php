<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
