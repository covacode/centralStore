<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessStockEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    public $tries = 3;
    public $backoff = [5, 30, 120];

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->connection = 'database';
        $this->onQueue('stock-sync');
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            $eventUuid = $this->payload['event_uuid'] ?? null;
            $storeId = $this->payload['store'] ?? null;
            $productId = $this->payload['product'] ?? null;

            // Verificar que tenemos los datos necesarios
            if (!$eventUuid || !$storeId || !$productId) {
                Log::warning('ProcessStockEvent: Missing required data', $this->payload);
                $this->logSyncEvent($eventUuid, null, null, 'ignored', 'Missing required data');
                return;
            }

            // Verificar si ya procesamos este evento
            $existingLog = DB::table('stock_sync_log')
                ->where('event_uuid', $eventUuid)
                ->first();

            if ($existingLog) {
                Log::info('ProcessStockEvent: Event already processed', ['event_uuid' => $eventUuid]);
                return;
            }

            // Buscar el stock existente
            $stock = DB::table('stocks')
                ->where('store', $storeId)
                ->where('product', $productId)
                ->first();

            if (!$stock) {
                Log::warning('ProcessStockEvent: Stock not found', [
                    'store' => $storeId,
                    'product' => $productId
                ]);
                $this->logSyncEvent($eventUuid, $storeId, $productId, 'not_found', 'Stock record not found');
                return;
            }

            // Actualizar stock
            $updateData = [
                'updated_at' => Carbon::now(),
            ];

            // Mapear campos del payload a la tabla stocks
            if (isset($this->payload['available_quantity'])) {
                $updateData['available_quantity'] = (int) $this->payload['available_quantity'];
            }

            if (isset($this->payload['reserved_quantity'])) {
                $updateData['reserved_quantity'] = (int) $this->payload['reserved_quantity'];
            }

            if (isset($this->payload['total_quantity'])) {
                $updateData['total_quantity'] = (int) $this->payload['total_quantity'];
            }

            // Actualizar stock en la base de datos
            DB::table('stocks')
                ->where('store', $storeId)
                ->where('product', $productId)
                ->update($updateData);

            // Registrar el evento como procesado
            $this->logSyncEvent($eventUuid, $storeId, $productId, 'processed', 'Stock updated successfully');

            DB::commit();

            Log::info('ProcessStockEvent: Stock updated successfully', [
                'store' => $storeId,
                'product' => $productId,
                'event_uuid' => $eventUuid
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('ProcessStockEvent: Error processing stock event', [
                'error' => $e->getMessage(),
                'payload' => $this->payload
            ]);

            // Registrar el error
            $this->logSyncEvent(
                $this->payload['event_uuid'] ?? null,
                $this->payload['store'] ?? null,
                $this->payload['product'] ?? null,
                'error',
                $e->getMessage()
            );

            throw $e;
        }
    }

    private function logSyncEvent($eventUuid, $storeId, $productId, $status, $message)
    {
        try {
            DB::table('stock_sync_log')->insert([
                'event_uuid' => $eventUuid,
                'store' => $storeId,
                'product' => $productId,
                'status' => $status,
                'payload' => json_encode($this->payload),
                'message' => $message,
                'processed_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessStockEvent: Error logging sync event', [
                'error' => $e->getMessage(),
                'event_uuid' => $eventUuid
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('ProcessStockEvent: Job failed', [
            'error' => $exception->getMessage(),
            'payload' => $this->payload
        ]);
    }
}
