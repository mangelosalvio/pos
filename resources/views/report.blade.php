<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report</title>
    <style>
        body{
            font-size: 11px;
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
        }
        table thead td{
            font-weight: bold;
            border-bottom:1px solid #000;
        }
        table td{
            padding:3px 5px;
        }
        table tfoot td{
            border-top: 1px solid #000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
<script>
    function printPage() { print(); } //Must be present for Iframe printing
</script>
</html>