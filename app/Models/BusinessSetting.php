<?php
namespace App\Models;
use App\Traits\Translatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class BusinessSetting extends Model
{
    use HasFactory, Translatable; 
    protected $fillable = [
        'phone_number',
        'address',
        'business_hours',
        'website_url',
        'top_image_path',
        'site_public',
        'reply_email',
        'google_analytics_id',
    ];
    protected $casts = [
        'business_hours' => 'array',
        'site_public' => 'boolean',
    ];
    protected $with = ['translations'];
    protected function getTranslatableFields(): array
    {
        return [
            'shop_name',
            'access_information',
            'site_name',
            'shop_description',
            'terms_of_use',
            'privacy_policy',
            'address',
        ];
    }
    protected function getDefaultTranslation(string $attribute)
    {
        $defaults = [
            'shop_name' => 'Default Shop Name',
            'site_name' => 'Default Site Name',
            'shop_description' => 'No description available.',
            'access_information' => 'No access information available.',
            'terms_of_use' => 'No terms of use available.',
            'privacy_policy' => 'No privacy policy available.',
        ];
        return $defaults[$attribute] ?? null;
    }
    public function getPathTopImageUrlAttribute()
    {
        if (!$this->top_image_path) {
            return null;
        }
        if (filter_var($this->top_image_path, FILTER_VALIDATE_URL)) {
            return $this->top_image_path;
        }
        return Storage::disk('s3')->url($this->top_image_path);
    }
}