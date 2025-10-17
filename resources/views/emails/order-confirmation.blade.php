<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation - {{ $order->order_number }}</title>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p>Thank you for your purchase!</p>
    </div>

    <div class="order-details">
        <h2>Order Details</h2>
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Customer:</strong> {{ $order->buyer->name }}</p>
        <p><strong>Email:</strong> {{ $order->buyer->email }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    </div>

    <div class="order-details">
        <h2>Items Ordered</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Seller</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->seller->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }} BDT</td>
                    <td>{{ number_format($item->price * $item->quantity, 2) }} BDT</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; text-align: right;">
            <p class="total">Total Amount: {{ number_format($order->total_amount, 2) }} BDT</p>
        </div>
    </div>

</body>
</html>
