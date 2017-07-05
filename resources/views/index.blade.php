<!doctype html>
<html lang="en" ng-app="cashier">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apollo</title>

    {!! HTML::script('bower_components/webcomponentsjs/webcomponents.min.js') !!}

    <link rel="import" href="bower_components/iron-flex-layout/classes/iron-flex-layout.html">
    <link rel="import" href="bower_components/iron-icons/iron-icons.html"/>
    <link rel="import" href="bower_components/iron-icons/communication-icons.html">
    <link rel="import" href="bower_components/iron-icons/image-icons.html">

    <link rel="import" href="bower_components/paper-card/paper-card.html"/>
    <link rel="import" href="bower_components/paper-input/paper-input.html"/>
    <link rel="import" href="bower_components/paper-button/paper-button.html"/>
    <link rel="import" href="bower_components/paper-menu/paper-menu.html"/>
    <link rel="import" href="bower_components/paper-item/paper-item.html"/>
    <link rel="import" href="bower_components/paper-drawer-panel/paper-drawer-panel.html"/>
    <link rel="import" href="bower_components/paper-header-panel/paper-header-panel.html"/>
    <link rel="import" href="bower_components/paper-icon-button/paper-icon-button.html"/>
    <link rel="import" href="bower_components/paper-scroll-header-panel/paper-scroll-header-panel.html"/>
    <link rel="import" href="bower_components/paper-styles/color.html"/>
    <link rel="import" href="/bower_components/iron-icons/social-icons.html">

    <link rel="stylesheet" href="bower_components/angular-material/angular-material.css"/>

    <script src="bower_components/angular/angular.min.js"></script>
    <script src="bower_components/angular-messages/angular-messages.min.js"></script>
    <script src="bower_components/angular-route/angular-route.min.js"></script>
    <script src="bower_components/angular-animate/angular-animate.js"></script>
    <script src="bower_components/angular-aria/angular-aria.js"></script>
    <script src="bower_components/angular-material/angular-material.min.js"></script>
    <script src="bower_components/angularUtils-pagination/dirPagination.js"></script>


    <script src="bower_components/pouchdb/dist/pouchdb.min.js"></script>
    <script src="bower_components/angular-pouchdb/angular-pouchdb.min.js"></script>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/moment/min/moment.min.js"></script>

    <script src="node_modules/numeral/min/numeral.min.js"></script>

    <script src="js/app.js"></script>

    <!-- Controllers -->
    <script src="js/MainController.js"></script>
    <script src="js/TableFileController.js"></script>
    <script src="js/TableController.js"></script>
    <script src="js/ProductsFileController.js"></script>
    <script src="js/CategoryFileController.js"></script>
    <script src="js/SaleInventoryController.js"></script>
    <script src="js/UserFileController.js"></script>
    <script src="js/SalesFileController.js"></script>
    <script type="text/javascript">

        function PrintElem(elem)
        {
            Popup($(elem).html());
        }

        function Popup(data)
        {
            var mywindow = window.open('', 'my div', 'height=400,width=600');
            mywindow.document.write('<html><head><title>my div</title>');
            mywindow.document.write('<link rel="stylesheet" href="css/report.css" type="text/css" />');
            //mywindow.document.write('<style>* { font-size: 11px; font-family:Arial;}</style>');
            mywindow.document.write('</head><body >');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10

            mywindow.print();
            //mywindow.close();

            return true;
        }

    </script>


    <style is="custom-style">

        body {
            font-family: 'Roboto', 'Noto', sans-serif;
            font-size: .9vw;
            margin: 0;
            background-color: var(--paper-grey-50);
        }

        section {
            padding: 20px 0;
        }

        section > div {
            padding: 14px;
            font-size: 16px;
        }

        .cards {
            @apply(--layout-horizontal);
            @apply(--center-justified);
            margin:3px;
            font-size:.7vw;
        }

        .sales-table{
            width:100%;
        }

        .sales-table td{
            width:15%;
        }

        .sales-table thead td{
            padding:3px;
            font-weight: bold;
            text-align: right;
        }

        .sales-table thead td:nth-child(1),.sales-table thead td:nth-child(2){
            text-align:left;
        }

        .sales-table tbody td{
            padding:3px;
            text-align: right;
        }

        .sales-table tbody td:nth-child(1),.sales-table tbody td:nth-child(2){
            text-align: left;
        }

        paper-card{
            margin:3px 0px;
        }

        .total-amount{
            text-align: right; font-size:40px; font-weight:bold;
            color:var(--paper-grey-50);

        }

        .total-amount-container{
            background-color: var(--paper-purple-800);
        }

        .table-cards-container{ margin-bottom: 8px; }



        .product-item-container md-card{ background-color: var(--paper-blue-500); color:#fff; text-align: center;}
        .product-item-container md-card md-card-content { text-align:center; }
        .category-container md-card{ background-color: var(--paper-blue-grey-500); color:#fff; text-align: center;}
        .category-container-item-container md-card md-card-content { text-align:center; }

        .main-container{
            @apply('--layout-self-stretch');
        }

        paper-header-panel{

        }

        paper-toolbar{
            background-color: var(--google-green-300);
            font-weight:bold;
            font-size:18px;
            color:#fff;
        }

        .orders-container paper-card{
            --paper-card-header-text:{
                font-size:14px;
                padding:8px;
             };
        }

        a md-card-content{
            text-decoration: none;
            color:#000;
        }

        .table-cards-container md-card{
            text-align: center;
        }
        a{
            text-decoration: none;
        }

        .vacant{ background-color: var(--paper-green-500); }
        .bill-out{ background-color: var(--google-red-500); }
        .taken{ background-color: var(--paper-yellow-500); }

        .product{ font-size: .7vw; text-align: center; }

        .functions-container {
            font-size:1vw; text-align: center;
        }

        .order-table{ border-collapse: collapse; }

        .order-table tfoot td{
            border-top:1px solid #c0c0c0;
            font-weight: bold;;
        }

        paper-drawer-panel{
        --paper-drawer-panel-left-drawer-container: {
            z-index: 1000;
         };
        }
    </style>
    <script type="text/javascript">
        function printIframe(id)
        {
            var iframe = document.frames ? document.frames[id] : document.getElementById(id);
            var ifWin = iframe.contentWindow || iframe;
            iframe.focus();
            ifWin.printPage();
            return false;
        }
    </script>
</head>

<body class="fullbleed" ng-controller="mainController">
<paper-drawer-panel class="flex" force-narrow="true">
    <paper-header-panel drawer>
        <div>
            @include('menu')
        </div>
    </paper-header-panel>
    <paper-scroll-header-panel main class="flex">
        <paper-toolbar class="layout horizontal center">
            <paper-icon-button icon="menu" paper-drawer-toggle></paper-icon-button>
            <div>{{ Config::get('constants.COMPANY_NAME') }}</div>
            <div class="flex"></div>
            <a href="{{ url('/auth/logout') }}" style="padding-right: 20px;">
            <iron-icon icon="close" style="color:#fff;"></iron-icon>
            </a>
        </paper-toolbar>
        @yield('content')
    </paper-scroll-header-panel>
</paper-drawer-panel>


</body>
</html>