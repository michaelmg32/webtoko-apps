<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print - Struk Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .receipt {
            text-align: center;
            font-size: 14pt;
            line-height: 1.5;
        }
        .receipt-header {
            margin-bottom: 12pt;
            font-size: 14pt;
        }
        .receipt-divider {
            border-top: 2px dashed #000;
            margin: 10pt 0;
        }
        .receipt-info {
            text-align: left;
            font-size: 13pt;
            margin-bottom: 12pt;
        }
        .receipt-items {
            margin-bottom: 12pt;
            text-align: left;
            font-size: 13pt;
        }
        .receipt-item {
            margin-bottom: 8pt;
        }
        .receipt-item-name {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3pt;
        }
        .receipt-item-detail {
            font-size: 12pt;
            color: #000;
            display: flex;
            justify-content: space-between;
        }
        .receipt-summary {
            text-align: right;
            font-size: 13pt;
            margin-bottom: 12pt;
        }
        .receipt-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5pt;
        }
        .receipt-total {
            font-size: 15pt;
            border-top: 2px solid #000;
            padding-top: 8pt;
            margin-top: 8pt;
            font-weight: bold;
        }
        .receipt-footer {
            text-align: center;
            font-size: 12pt;
            margin-top: 12pt;
        }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 2mm;
            }
            .receipt {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">
    <div class="receipt">
        <div class="receipt-header">
            <div>Bukit Foto Studio Cab Km9</div>
            <div style="font-size: 12pt; font-weight: normal; margin-top: 3pt;">Jl. Kolonel H. Barlian, Kebun Bunga, Kec. Sukarami, Kota Palembang, Sumatera Selatan 30152</div>
            <div style="font-size: 11pt; margin-top: 2pt;">Telp: 0851-5695-6302</div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-info">
            <div style="display: flex; justify-content: space-between; margin-bottom: 3pt;">
                <span>{{ $order->created_at->format('Y-m-d') }}</span>
                <span>{{ $order->created_at->format('H:i:s') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 3pt;">
                <span>No.</span>
                <span>{{ $order->order_code }}</span>
            </div>
            <div>Customer: {{ $order->customer_name }}</div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-items">
            @foreach($order->items as $item)
                @php
                    $productName = $item->product_name ?? ($item->product->name ?? 'Item');
                    $qty = $item->quantity;
                    $price = $item->price;
                    $discount = $item->discount ?? 0;
                    $finalPrice = max(0, $price - $discount);
                    $subtotal = $item->subtotal;
                @endphp
                <div class="receipt-item">
                    <div class="receipt-item-name">
                        <span>{{ $productName }}</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="receipt-item-detail">
                        <span>{{ $qty }} x 
                            @if($discount > 0)
                                <span style="text-decoration: line-through; color: red;">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                <span style="color: red; margin-left: 5px;">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            @else
                                Rp {{ number_format($price, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-summary">
            @php
                $discountAmount = $order->discount_amount ?? 0;
                $totalPrice = $order->total_price;
                $subtotalAll = $totalPrice + $discountAmount;
                $dpAmount = $order->dp_amount ?? 0;
                
                $paymentsPaidAmount = $order->payments ? $order->payments->sum('amount') : 0;
                $totalPaid = $dpAmount > 0 ? $dpAmount : $paymentsPaidAmount;
            @endphp

            <div class="receipt-summary-row">
                <span>Sub Total</span>
                <span>Rp {{ number_format($subtotalAll, 0, ',', '.') }}</span>
            </div>
            
            @if($discountAmount > 0)
            <div class="receipt-summary-row">
                <span>Diskon</span>
                <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="receipt-summary-row receipt-total">
                <span>Total</span>
                <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>
            
            @if($dpAmount > 0)
            <div class="receipt-summary-row" style="margin-top: 8pt;">
                <span>DP Terbayar</span>
                <span>- Rp {{ number_format($dpAmount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="receipt-summary-row" style="margin-top: 8pt;">
                <span>
                    @if($dpAmount > 0)
                        Sisa Bayar
                    @else
                        Bayar
                    @endif
                </span>
                <span>Rp 
                    @if($dpAmount > 0)
                        {{ number_format($totalPrice - $dpAmount, 0, ',', '.') }}
                    @else
                        @php
                            $lastPayment = $order->payments->last();
                            $lastAmount = $lastPayment ? $lastPayment->amount : $totalPrice;
                        @endphp
                        {{ number_format($lastAmount, 0, ',', '.') }}
                    @endif
                </span>
            </div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-footer">
            <div>Terima kasih telah berbelanja</div>
            <div style="margin-top: 5pt;">Bukit Foto Studio Cab Km9</div>
        </div>
    </div>
</body>
</html>
