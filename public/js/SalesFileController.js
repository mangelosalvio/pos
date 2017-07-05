( function(){
    angular
        .module('cashier')
        .controller('SalesFileController',[
            '$scope',
            '$mdDialog',
            'sales',
            function( $scope, $mdDialog, sales ){
                $scope.salesPerPage = 15;
                $scope.totalSales = 0;
                $scope.sales = [];
                $scope.search = '';

                /*sales.getSales()
                    .then(function(result){
                        $scope.sales = result.data;
                    });*/

                $scope.pageChanged = function(newPage){
                    $scope.getResultsPage(newPage);
                };

                $scope.getResultsPage = function(pageNumber){
                    console.log($scope.search);
                    sales.getSales(pageNumber, $scope.search)
                        .then(function(result){
                            $scope.sales = result.data.data;
                            $scope.totalSales = result.data.total;
                        });
                };

                $scope.viewSale = function(sale){
                    $mdDialog.show({
                        controller : SaleDialogController,
                        templateUrl : 'pages/dialog.sale.html',
                        clickOutsideToClose : false,
                        sale : sale
                    }).then(function(result){

                    });
                };

                $scope.searchSales = function(){
                    $scope.getResultsPage(1);
                };

                $scope.searchOnEnter = function($event){
                    if ( $event.keyCode == 13 ) {
                        $scope.getResultsPage(1);
                    }
                };

                $scope.getResultsPage(1);
            }
        ]);

    function SaleDialogController($scope, $mdDialog, sales,  sale ){

        $scope.sale = {};

        sales.getSale(sale.id)
            .then(function(result){
                $scope.sale = result.data[0];
                console.log($scope.sale);
            });

        $scope.done = function() {
            $mdDialog.hide();
        };
    }
} )();