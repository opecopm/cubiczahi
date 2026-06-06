<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Sales Invoice #{{ $salesInvoice->reference }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .header .top {
            display: flex;
            background: white;
            z-index: 10;
        }

        .header .top .logo {
            text-align: left;
            color: #333;
        }

        .header .top .order-info {
            text-align: right;
            color: #666;
        }

        .header .top .order-info h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .header .top .order-info p {
            margin: 0;
        }

        .page-header,
        .page-header-space {
            height: 320px;
        }

        .page-footer,
        .page-footer-space {
            height: 50px;
        }

        .page-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid black;
            background: white;
            z-index: 10;
            text-align: center;
        }

        .page-header {
            position: fixed;
            top: 0;
            width: 100%;
            border-bottom: 1px solid lightgray;
            background: white;
            z-index: 10;
        }

        .order-details,
        .customer-details {
            margin: 20px 0;
        }

        .order-details h3,
        .customer-details h3 {
            color: lightgray;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .order-details table,
        .customer-details table {
            width: 100%;
        }

        .order-details table td,
        .customer-details table td {
            padding: 5px 0;
        }

        .sales-order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sales-order-table th {
            border: 1px solid #ddd;
            padding: 10px;
            vertical-align: top;
        }

        .sales-order-table th {
            background-color: #206bc4;
            color: #ffffff;
            text-align: left;
        }

        .sales-order-table td {
            border: 1px solid #ddd;
            padding: 10px !important;
            vertical-align: top;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-section p {
            font-size: 16px;
            margin: 5px 0;
        }

        .total-section h3 {
            font-size: 20px;
            color: #333;
        }

        .signature-section {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 10;
        }

        .footer p {
            margin: 5px 0;
        }

        .address-section {
            line-height: 1.5;
            font-size: 14px;
            color: #333;
        }

        .address-section strong {
            display: inline-block;
            min-width: 70px;
        }

        .page {
            page-break-after: always;
        }

        @page {
            margin: 5mm;
        }

        @media print {
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            button {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="top" style="display: flex; border-bottom: 2px solid #206bc4;">
            <div class="logo" style="width: 50%; text-align:left">
                <h2 style="text-align:left">
                    {{ $salesInvoice->company?->getTranslation('name', 'en') }}
                </h2>
                <p style="text-align:left">
                    VAT No: {{ $salesInvoice->company?->trn ?? '-' }}
                </p>
                <p style="text-align:left">
                    CR No: {{ $salesInvoice->company?->crn ?? '-' }}
                </p>
            </div>
            <div class="order-info" style="width: 50%; text-align:right">
                <h2 style="text-align:right">Sales Invoice</h2>
                <p style="text-align:right">
                    Invoice No. {{ $salesInvoice->reference }}
                </p>
            </div>
        </div>
        <div class="row" style="display: flex">
            <div style="width: 50%">
                 <!-- Customer Information -->
                <div class="customer-info customer-details">
                    <h3>Customer Information</h3>
                    <strong>Name:</strong> {{ $salesInvoice->customer?->name }}<br>
                    <strong>Phone:</strong> {{ $salesInvoice->customer?->phone }}<br>
                    <strong>VAT No:</strong> {{ $salesInvoice->customer?->trn ?? '-' }}<br>
                    <strong>CR No:</strong> {{ $salesInvoice->customer?->crn ?? '-' }}<br>
                    <strong>Address:</strong> 
                    @php
                        $billingAddress = $salesInvoice->customer?->billingAddress();
                    @endphp
                    @if($billingAddress)
                        {{ trim($billingAddress->line1 . ' ' . $billingAddress->line2) }},
                        {{ trim($billingAddress->city . ' ' . $billingAddress->state . ' ' . $billingAddress->postal_code) }},
                        {{ $billingAddress->country }}
                    @else
                        -
                    @endif
                    <br>
                </div>
            </div>
            <div style="width: 50%">
                 <!-- Order Information -->
                 <div class="order-info order-details">
                    <h3>Invoice Information</h3>
                    <strong>Invoice Date:</strong> @if ($salesInvoice->issued_at) {{ date('Y-m-d',strtotime($salesInvoice->issued_at)) }}

                    @endif<br>
                    <strong>Status:</strong> {{ $salesInvoice->status }}<br>
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        Thank you for your business!<br>
        {{ $salesInvoice->company?->getTranslation('name', 'en') }}
        | {{ $salesInvoice->company?->website ?? 'www.example.com' }}
        | {{ $salesInvoice->company?->email ?? 'support@example.com' }}
        | {{ trim($salesInvoice->company?->primaryAddress?->line1 . ' ' . $salesInvoice->company?->primaryAddress?->line2) ?: 'Address not set' }},
        {{ $salesInvoice->company?->primaryAddress?->city ?? '' }},
        {{ $salesInvoice->company?->primaryAddress?->country ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <td>
                    <div class="page-header-space"></div>
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    <div class="container">
                        <!-- Sales Order Items Table -->
                        <div class="order-details">
                            <h3 style="padding-bottom: 0px;">Order Details</h3>
                            <table class="sales-order-table" style="margin-top: 10px">
                                <thead>
                                    <tr>
                                        <th>Sn.</th>
                                        <th>Item Description</th>
                                        <th style="text-align: right; min-width:90px;">Start</th>
                                        <th style="text-align: right; min-width:90px;">End</th>
                                        <th style="text-align: center">Qty</th>
                                        <th style="text-align: right">U.&nbsp;Price</th>
                                        <th style="text-align: right">Discount</th>
                                        <th style="text-align: right">T.&nbsp;Price</th>
                                        <th style="text-align: right">Tax</th>
                                        <th style="text-align: right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesInvoice->items as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td style="min-width: 200px;">
                                            <strong>{{ $item->name }}</strong><br>
                                            {{ $item->description }}
                                        </td>
                                        <td style="text-align: right">{{$item->rental_start_at_formatted??$item->rental_start_at}}</td>
                                        <td style="text-align: right">{{$item->rental_end_at_formatted??$item->rental_end_at}}</td>
                                        <td style="text-align: center">{{ $item->quantity }} {{$item->unit}}</td>
                                        <td style="text-align: right">{{ number_format($item->price, 2) }}</td>
                                        <td style="text-align: right">{{ number_format($item->discount ?? 0, 2) }}</td>
                                        <td style="text-align: right">{{ number_format($item->subtotal, 2) }}</td>
                                        <td style="text-align: right">
                                            {{ number_format($item->tax, 2) }}
                                            <br><small class="text-xs">{{ number_format($item->tax_rate, 0) ?? '' }}% {{ $item->tax_name ?? '' }}</small>
                                        </td>
                                        <td style="text-align: right">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Section -->
                        <div class="total-section">
                            <table style="width: 100%;">
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td style="float:right;">
                                        <table class="table-total">
                                            <tr>
                                                <td><strong>Subtotal:</strong></td>
                                                <td>{{ number_format($salesInvoice->total_price, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Discount:</strong></td>
                                                <td>{{ number_format($salesInvoice->discount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Taxable Amount:</strong></td>
                                                <td>{{ number_format($salesInvoice->subtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Tax:</strong></td>
                                                <td>{{ number_format($salesInvoice->tax, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td><h3><strong>Grand Total:</strong></h3></td>
                                                <td><h3>{{ number_format($salesInvoice->total, 2) }}</h3></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- Signature Section -->
                        <div class="signature-section" style="margin-top: 100px;">
                            <div class="signature-box">Customer Signature</div>
                            <div class="signature-box">Authorized Signature</div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td>
                    <div class="page-footer-space"></div>
                </td>
            </tr>
        </tfoot>
    </table>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
