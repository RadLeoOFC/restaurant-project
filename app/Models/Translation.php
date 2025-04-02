<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['language_id', 'key', 'value'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public static function getValue($key, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $languageId = Language::where('code', $locale)->value('id');

        return self::where('key', $key)
            ->where('language_id', $languageId)
            ->value('value') ?? $key;
    }
}

