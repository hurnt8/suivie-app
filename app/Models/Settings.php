<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $company_name
 * @property string|null $logo_path
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $domain
 * @property string $tracking_prefix
 * @property string $currency
 * @property string $default_locale
 * @property string|null $mail_from_address
 * @property string|null $mail_from_name
 * @property string|null $smtp_host
 * @property int|null $smtp_port
 * @property string|null $smtp_username
 * @property string|null $smtp_password
 * @property string|null $smtp_encryption
 */
#[Fillable([
    'company_name', 'logo_path', 'address', 'phone', 'email', 'domain', 'tracking_prefix',
    'currency', 'default_locale', 'mail_from_address', 'mail_from_name',
    'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption',
])]
class Settings extends Model
{
    protected static ?self $cached = null;

    protected function casts(): array
    {
        return [
            'smtp_password' => 'encrypted',
        ];
    }

    /**
     * The application only ever has one settings row (id = 1). Memoized for
     * the lifetime of the current process/request only — deliberately not
     * cached across requests, since caching a serialized Eloquent model in
     * a shared store (database/file) is fragile across PHP processes.
     */
    public static function current(): self
    {
        return self::$cached ??= self::query()->firstOrCreate(['id' => 1], [
            'company_name' => 'Livrion',
            'tracking_prefix' => config('tracking.default_prefix', 'LVR'),
            'currency' => 'EUR',
            'default_locale' => config('app.locale', 'fr'),
        ]);
    }

    /**
     * Drops the memoized row. Called between tests so a settings row created
     * in one test never leaks into the next (the DB is rolled back, the static isn't).
     */
    public static function flush(): void
    {
        self::$cached = null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::$cached = null);
        static::deleted(fn () => self::$cached = null);
    }

    public function usesCustomSmtp(): bool
    {
        return filled($this->smtp_host);
    }
}
