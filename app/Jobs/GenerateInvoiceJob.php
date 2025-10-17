<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Jobs;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Class GenerateInvoiceJob
 *
 * Queued job that generates a text invoice file for a specific paid order.
 * Invoices are saved to storage/app/invoices/.
 *
 * This job is idempotent — if an invoice already exists, it will skip regeneration.
 */
class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    protected Order $order;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\OrderRepository $orderRepository
     * @return void
     */
    public function handle(OrderRepository $orderRepository): void
    {
        try {
            // Ensure order still exists and is paid
            $order = $orderRepository->find($this->order->id);
            if (! $order || $order->status !== 'paid') {
                Log::warning("Skipping invoice generation — order not found or not paid: ID {$this->order->id}");
                return;
            }

            // Define invoice directory and file path
            $directory = 'invoices';
            $fileName = "invoice_{$order->order_number}_{$order->id}.txt";
            $path = "{$directory}/{$fileName}";

            // Prevent duplicate invoice generation
            if (Storage::exists($path)) {
                Log::info("Invoice already exists for order ID {$order->id}, skipping generation.");
                return;
            }

            // Create directory if not exists
            Storage::makeDirectory($directory);

            // Generate invoice content
            $content = $this->generateInvoiceText($order);

            // Store invoice file
            Storage::put($path, $content);

            // Mark order as invoiced
            $orderRepository->markInvoiced($order);

            Log::info("Invoice generated successfully for order ID {$order->id}: {$path}");
        } catch (Throwable $e) {
            Log::error("Failed to generate invoice for order ID {$this->order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate plain text invoice content.
     *
     * @param \App\Models\Order $order
     * @return string
     */
    protected function generateInvoiceText(Order $order): string
    {
        $lines = [
            "===============================",
            "        INVOICE DETAILS        ",
            "===============================",
            "Invoice Date: " . now()->toDateTimeString(),
            "Order ID: {$order->id}",
            "Order Number: {$order->order_number}",
            "Buyer ID: {$order->buyer_id}",
            "Total Amount: {$order->total_amount}",
            "Status: {$order->status}",
            "===============================",
            "Thank you for your purchase!",
        ];

        return implode("\n", $lines) . "\n";
    }
}
