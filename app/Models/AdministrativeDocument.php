<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class AdministrativeDocument extends Model
{
    use SoftDeletes;

    const TYPE_INVOICE = 'invoice';
    const TYPE_CREDIT_NOTE = 'credit_note';
    const TYPE_DELIVERY_ORDER = 'delivery_order';
    const TYPE_TERMS = 'terms';
    const TYPE_EXIT_ORDER = 'exit_order';

    /**
     * Prefix used in the human-readable document number (IN-0001, etc.).
     */
    public static $prefixes = [
        self::TYPE_INVOICE        => 'IN',
        self::TYPE_CREDIT_NOTE    => 'NC',
        self::TYPE_DELIVERY_ORDER => 'OE',
        self::TYPE_TERMS          => 'TC',
        self::TYPE_EXIT_ORDER     => 'OS',
    ];

    /**
     * Human label for each type — shown in menus, breadcrumbs, list headers.
     */
    public static $labels = [
        self::TYPE_INVOICE        => 'Nota de Entrega / Invoice',
        self::TYPE_CREDIT_NOTE    => 'Nota de Crédito',
        self::TYPE_DELIVERY_ORDER => 'Orden de Entrega',
        self::TYPE_TERMS          => 'Términos y Condiciones',
        self::TYPE_EXIT_ORDER     => 'Orden de Salida',
    ];

    protected $fillable = [
        'type',
        'number',
        'company',
        'parent_document_id',
        'data',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_document_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Next correlative for a given document type. Wrapped in a transaction
     * by the caller with SELECT ... FOR UPDATE for concurrency safety.
     */
    public static function nextNumber(string $type): int
    {
        $last = static::withTrashed()->where('type', $type)->max('number');
        return ($last ?? 0) + 1;
    }

    /**
     * Human-readable number with prefix (e.g. IN-0001).
     */
    public function getFormattedNumberAttribute(): string
    {
        $prefix = self::$prefixes[$this->type] ?? 'DOC';
        return $prefix . '-' . str_pad($this->number, 4, '0', STR_PAD_LEFT);
    }
}
