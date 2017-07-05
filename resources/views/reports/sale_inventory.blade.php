@extends('report')
@section('content')
    <div style="text-align: center;">
        <span style="font-weight: bold; font-size: 14px;">Sale Inventory Report</span> <br/>
        {{ $from_date->toFormattedDateString() }} to {{ $to_date->toFormattedDateString() }}
    </div>
    <table>
        <thead>
        <tr>
            <td>Product</td>
            <td style="text-align: right;">Amount</td>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $obj)
            <tr>
                <td>{{ $obj->product_desc }}</td>
                <td style="text-align:right;">{{ number_format($obj->quantity,2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td style="text-align:right;">{{ number_format($summary['total_quantity'],2) }}</td>
        </tr>
        </tfoot>
    </table>

@endsection