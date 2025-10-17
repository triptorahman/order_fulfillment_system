<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\OrderRepository;
use App\Jobs\GenerateInvoiceJob;

/**
 * Class ProcessInvoices
 *
 * Artisan command to process paid but uninvoiced orders and
 * dispatch invoice generation jobs. The invoices are created
 * as text files under storage/app/invoices/.
 *
 * Usage:
 *   php artisan orders:process-invoices
 *
 * Scheduled in Kernel to run daily.
 */
class ProcessInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch invoice generation jobs for all paid but uninvoiced orders';

    /**
     * The OrderRepository instance.
     *
     * @var \App\Repositories\OrderRepository
     */
    protected OrderRepository $orders;

    /**
     * Create a new command instance.
     *
     * @param \App\Repositories\OrderRepository $orders
     */
    public function __construct(OrderRepository $orders)
    {
        parent::__construct();
        $this->orders = $orders;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Retrieve paid but uninvoiced orders using repository
        $orders = $this->orders->findUninvoicedPaidOrders();

        if ($orders->isEmpty()) {
            $this->info('No paid but uninvoiced orders found.');
            return Command::SUCCESS;
        }

        foreach ($orders as $order) {
            GenerateInvoiceJob::dispatch($order);
            $this->info("Dispatched invoice generation job for Order ID: {$order->id}");
        }

        $this->info('All invoice jobs dispatched successfully!');
        return Command::SUCCESS;
    }
}
