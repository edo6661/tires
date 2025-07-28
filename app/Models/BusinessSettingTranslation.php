<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSettingTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_setting_id',
        'locale',
        'shop_name',
        'access_information',
        'site_name',
        'shop_description',
        'terms_of_use',
        'privacy_policy',
    ];

    public $timestamps = true;

    public function businessSetting(): BelongsTo
    {
        return $this->belongsTo(BusinessSetting::class);
    }
}