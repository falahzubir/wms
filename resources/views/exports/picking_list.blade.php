<style>
    table, th, td{
        border: 1px solid black;
    }
    th{
        text-align: left;
    }
</style>
<table style="border-collapse: collapse;">
    <thead style="background: #DCDCDC">
    <tr>
        <th><strong>Product</strong></th>
        <th><strong>L</strong></th>
        <th><strong>B</strong></th>
        <th><strong>Grand Total</strong></th>
    </tr>
    </thead>
    <tbody>
        @foreach($products as $name => $qty)
        <tr>
            <td style="background: #DCDCDC"><strong>{{ $name }}</strong></td>
            <td>{{ $qty['loose'] }}</td>
            <td>{{ $qty['box'] }}</td>
            <td>{{ $qty['loose'] + $qty['box'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="background: #DCDCDC"><strong>Grand Total</strong></td>
            <td>{{ $total_products['loose'] }}</td>
            <td>{{ $total_products['box'] }}</td>
            <td>{{ $total_products['loose'] + $total_products['box'] }}</td>
        </tr>
    </tfoot>
</table>
