<?php

namespace App\Models;

use App\Jobs\ProcessStockEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Stock extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'store',
        'product',
        'available_quantity',
        'reserved_quantity',
        'total_quantity',
        'sold_quantity'
    ];

    /**
     * Get the store that owns the stock.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store', 'id');
    }

    /**
     * Get the product that owns the stock.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName(Stock::class)->logAll();
    }

    /**
     * Synchronize stock data with external system.
     * @param Stock $stock
     * @return void
     */
    public static function synchronizeStock(Stock $stock): void
    {
        $data =  ProcessStockEvent::generatePayload([
            'store' => $stock->store,
            'product' => $stock->product,
            'available_quantity' => $stock->available_quantity,
            'reserved_quantity' => $stock->reserved_quantity,
            'total_quantity' => $stock->total_quantity,
            'sold_quantity' => $stock->sold_quantity,
        ]);

        ProcessStockEvent::dispatch($data);
    }
}
