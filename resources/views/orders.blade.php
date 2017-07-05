<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apollo</title>

    {!! HTML::script('bower_components/webcomponentsjs/webcomponents.min.js') !!}

    <link rel="import" href="bower_components/iron-flex-layout/classes/iron-flex-layout.html">
    <link rel="import" href="bower_components/iron-icons/iron-icons.html"/>

    <link rel="import" href="bower_components/paper-card/paper-card.html"/>
    <link rel="import" href="bower_components/paper-input/paper-input.html"/>
    <link rel="import" href="bower_components/paper-button/paper-button.html"/>
    <link rel="import" href="bower_components/paper-drawer-panel/paper-drawer-panel.html"/>
    <link rel="import" href="bower_components/paper-header-panel/paper-header-panel.html"/>
    <link rel="import" href="bower_components/paper-icon-button/paper-icon-button.html"/>
    <link rel="import" href="bower_components/paper-scroll-header-panel/paper-scroll-header-panel.html"/>



    <link rel="import" href="bower_components/paper-styles/color.html"/>

    <style is="custom-style">

        body {
            font-family: 'Roboto', 'Noto', sans-serif;
            font-size: 14px;
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
            width:80px;
            margin:3px;
            font-size:11px;
        }

        table{
            width:100%;
        }

        table td{
            width:15%;
        }
        tabe td:nth-child(2){
            width:40%;
        }

        table thead td{
            padding:3px;
            font-weight: bold;
            text-align: right;
        }

        table thead td:nth-child(2){
            text-align:left;
        }

        table tbody td{
            padding:3px;
            text-align: right;
        }
        table tbody td:nth-child(2){
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
        .table-cards-container paper-card{
            background-color: var(--paper-yellow-500);
            text-align: center;
        }

        .category-item-container paper-card{ background-color: var(--paper-blue-500); color:#fff; text-align: center;}
        .category-container paper-card{ background-color: var(--paper-blue-grey-500); color:#fff; text-align: center;}

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

    </style>
</head>
<body class="fullbleed">
<paper-drawer-panel class="flex" force-narrow="true">
    <paper-header-panel drawer>
        <div> Drawer content... </div>
    </paper-header-panel>
    <paper-scroll-header-panel main class="flex">
        <paper-toolbar class="layout horizontal center">
            <paper-icon-button icon="menu" paper-drawer-toggle></paper-icon-button>
            <div>Apollo Restaurant</div>
        </paper-toolbar>
        <div class="main-container flex" style="height:100%;">
            <div class="layout horizontal" style="height: 100%;" >
                <div class="flex-4 layout vertical" style="height:100%;">
                    <paper-card class="layout vertical fullbleed" style="height:70%" elevation="1">
                        <div class="card-content" style="height:100%;">
                            <div style="height:80%; overflow: auto;" class="layout vertical orders-container">

                                @for($i = 1 ; $i <= 3 ; $i++)
                                    <paper-card heading="Order #{{ $i }}" elevation="2">
                                        <div class="card-content">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <td>QTY</td>
                                                    <td>PRODUCT</td>
                                                    <td>PRICE</td>
                                                    <td>DISC</td>
                                                    <td>AMOUNT</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1.00</td>
                                                    <td>Bird's Nest</td>
                                                    <td>110.00</td>
                                                    <td>0.00</td>
                                                    <td>110.00</td>
                                                </tr>
                                                <tr>
                                                    <td>1.00</td>
                                                    <td>Bird's Nest</td>
                                                    <td>110.00</td>
                                                    <td>0.00</td>
                                                    <td>110.00</td>
                                                </tr>
                                                <tr>
                                                    <td>1.00</td>
                                                    <td>Bird's Nest</td>
                                                    <td>110.00</td>
                                                    <td>0.00</td>
                                                    <td>110.00</td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </paper-card>
                                @endfor
                            </div>
                        </div>
                    </paper-card>

                    <paper-card style="margin-top: 5px; height:10%;" elevation="2" class="total-amount-container" >
                        <div class="card-content">
                            <div class="horizontal layout center">
                                <div class="flex" style="color:#fff; text-align:right; padding-right:20px;">
                                    Total Amount
                                </div>
                                <div class="horizontal layout center" style="font-weight:bold; font-size:36px; color:#fff;">
                                    88,888.88
                                </div>
                            </div>

                        </div>
                    </paper-card>

                    <paper-input label="Product Code" autofocus></paper-input>

                </div>
                <div class="flex-8" style="padding:5px; height:100%; position: relative;">
                    <div class="layout horizontal wrap table-cards-container">
                        @for($i = 0 ; $i <= 20 ; $i++)
                            <paper-card animated-shadow="true" class="cards">
                                <div class="card-content">
                                    Table {{ $i + 1 }}
                                </div>
                            </paper-card>
                        @endfor
                    </div>
                    <div class="layout horizontal wrap category-item-container" style="max-height:55%; overflow: auto;">

                        @for($i = 0 ; $i <= 20 ; $i++)
                            <paper-card animated-shadow="true" class="cards category-items-1">
                                <div class="card-content">
                                    Item {{ $i + 1 }}
                                </div>
                            </paper-card>
                        @endfor
                    </div>


                    <div class="layout horizontal wrap category-container" style="position:absolute; bottom: 20px; left:0px;">
                        @for($i = 0 ; $i <= 10 ; $i++)
                            <paper-card animated-shadow="true" class="cards table-cards">
                                <div class="card-content">
                                    Categ {{ $i + 1 }}
                                </div>
                            </paper-card>
                        @endfor
                    </div>

                </div>

            </div>
        </div>
    </paper-scroll-header-panel>
</paper-drawer-panel>




</body>
</html>