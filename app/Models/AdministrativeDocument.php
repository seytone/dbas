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
    const TYPE_SHIPPING = 'shipping';
    const TYPE_SERVICE_ORDER = 'service_order';

    /**
     * Prefix used in the human-readable document number (IN-0001, etc.).
     */
    public static $prefixes = [
        self::TYPE_INVOICE        => 'IN',
        self::TYPE_CREDIT_NOTE    => 'NC',
        self::TYPE_DELIVERY_ORDER => 'OE',
        self::TYPE_TERMS          => 'TC',
        self::TYPE_EXIT_ORDER     => 'OS',
        self::TYPE_SHIPPING       => 'EN',
        self::TYPE_SERVICE_ORDER  => 'SV',
    ];

    /**
     * Nombre descriptivo que lleva el PDF al descargarse. Se prefiere sobre
     * el prefijo corto (in-0001.pdf, nc-0001.pdf…) porque el cliente
     * archiva los PDFs en carpetas y necesita reconocerlos de un vistazo.
     */
    public static $fileSlugs = [
        self::TYPE_INVOICE        => 'nota_entrega',
        self::TYPE_CREDIT_NOTE    => 'nota_credito',
        self::TYPE_DELIVERY_ORDER => 'orden_entrega',
        self::TYPE_EXIT_ORDER     => 'orden_salida',
        self::TYPE_SHIPPING       => 'guia_envio',
        self::TYPE_TERMS          => 'terminos_condiciones',
        self::TYPE_SERVICE_ORDER  => 'orden_servicio',
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
        self::TYPE_SHIPPING       => 'Guía de Envío',
        self::TYPE_SERVICE_ORDER  => 'Orden de Servicio',
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

    /**
     * Nombre del archivo al descargar el PDF, ej. "nota_entrega-0001.pdf".
     * Se conserva el guion antes del correlativo para que el número siga
     * mapeando a simple vista con el que muestra la app (IN-0001).
     */
    public function getFileNameAttribute(): string
    {
        $slug = self::$fileSlugs[$this->type] ?? 'documento';
        return $slug . '-' . str_pad($this->number, 4, '0', STR_PAD_LEFT) . '.pdf';
    }
}
