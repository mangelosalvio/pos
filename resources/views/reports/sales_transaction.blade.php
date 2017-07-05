@extends('report')
@section('content')
    <style>
        .report-table td:nth-child(n+3){
            text-align: right;
        }
        table a{
            color: #000;
        }
    </style>
    <div style="text-align: center;">
        <span style="font-weight: bold; font-size: 14px;">Sales Transaction</span> <br/>
        {{ $from_date->toFormattedDateString() }} to {{ $to_date->toFormattedDateString() }}
    </div>
    <table class="report-table">
        <thead>
        <tr>
            <td>SI#</td>
            <td>Date/Time</td>
            <td>Gross Amount</td>
            <td>Less Vat</td>
            <td>Discount Amount</td>
            <td>Net Amount</td>
            <td>Vatable Sales</td>
            <td>Vat</td>
            <td>Vat Exempt</td>
        </tr>
        </thead>
        <tbody>
        @foreach($Sales as $Sale)
            <tr>
                <td><a href="/report/invoice/{{ $Sale->id }}">{{ str_pad($Sale->id,7,0,STR_PAD_LEFT) }}</a></td>
                <td nowrap>{{ $Sale->sale_datetime->toDayDateTimeString() }}</td>
                <td>{{ number_format($Sale->gross_amount,2) }}</td>
                <td>{{ number_format($Sale->vat_discount_amount,2) }}</td>
                <td>{{ number_format($Sale->discount_amount,2) }}</td>
                <td>{{ number_format($Sale->net_amount,2) }}</td>
                <td>{{ number_format($Sale->vat_sales_amount,2) }}</td>
                <td>{{ number_format($Sale->vat_amount,2) }}</td>
                <td>{{ number_format($Sale->vat_exempt_amount,2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td>{{ number_format($Sales->sum('gross_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('vat_discount_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('discount_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('net_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('vat_sales_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('vat_amount'),2) }}</td>
            <td>{{ number_format($Sales->sum('vat_exempt_amount'),2) }}</td>
        </tr>
        </tfoot>
    </table>

@endsection