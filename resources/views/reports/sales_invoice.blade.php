@extends('report')
@section('content')
    <style>
        .report-table td:nth-child(1),.report-table td:nth-child(n+3){
            text-align: right;
        }
        table a{
            color: #000;
        }

        table.report-table tfoot td{
            border:none;
            text-align: right;
        }

        table.report-table tfoot tr:first-child td{
            border-top: 1px solid #000;
        }
    </style>
    <table>
        <tr>
            @if( $Sale->customer_name != '' )
                <td colspan="2">Customer:</td>
                <td>{{ $Sale->customer_name }}</td>
            @endif
        </tr>
        <tr>
            @if( $Sale->table != null )
                <td colspan="2">Table:</td>
                <td style="font-size: 14px; font-weight: bold;">{{ $Sale->table->table_desc }}</td>
            @else
                <td colspan="2">Table:</td>
                <td>TAKE-OUT</td>
            @endif
        </tr>
    </table>
    <table class="report-table">
        <thead>
        <tr>
            <td colspan="4" style="text-align: center;">{{ $Sale->sale_datetime->format("m/d/Y h:i:s") }} OS#<span style="font-size:14px;">{{ str_pad($Sale->id,7,0,STR_PAD_LEFT) }}</span></td>
        </tr>
        <tr>
            <td>Qty</td>
            <td>Description</td>
            <td>Price</td>
            <td>Amount</td>
        </tr>
        </thead>
        <tbody>
        @foreach($Sale->products as $item)
            <tr>
                <td>{{ round($item->pivot->quantity,0) }}</td>
                <td>{{ $item->product_desc }}</td>
                <td>{{ number_format($item->pivot->price,2) }}</td>
                <td>{{ number_format($item->pivot->price * $item->pivot->quantity,2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        @if( $Sale->gross_amount != $Sale->net_amount )
        <tr>
            <td colspan="3">Gross Amount</td>
            <td>{{ number_format($Sale->gross_amount,2) }}</td>
        </tr>
        @endif
        @if( $Sale->vat_discount_amount > 0 )
        <tr>
            <td colspan="3">Less Vat</td>
            <td>{{ number_format($Sale->vat_discount_amount,2) }}</td>
        </tr>
        @endif
        @if( $Sale->discount_amount > 0 )
        <tr>
            <td colspan="3">Less Disc</td>
            <td>{{ number_format($Sale->discount_amount,2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="3">Amount Due</td>
            <td>{{ number_format($Sale->net_amount,2) }}</td>
        </tr>

        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Cash</td>
            <td>{{ number_format($Sale->cash_amount,2) }}</td>
        </tr>

        <tr>
            <td colspan="3">Change</td>
            <td>{{ number_format($Sale->change_amount,2) }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;"><a href="#" onclick="window.history.back();">Back</a> </td>
        </tr>
        </tfoot>
    </table>
    <script>
        ( function(){
            window.scrollTo(0,0);
        } )();
    </script>
@endsection