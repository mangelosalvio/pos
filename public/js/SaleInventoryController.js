( function(){
    angular
        .module('cashier')
        .factory('report', function($http){
            var report = {};

            report.getSaleInventory = function(from_date, to_date){
                return $http.get('/sales/sale-inventory/' + from_date + '/' + to_date);
            };

            report.getDailySales = function(from_date, to_date){
                return $http.get('/sales/daily-sales/' + from_date + '/' + to_date);
            };

            return report;
        })
        .controller('SaleInventoryController' ,['$scope','report','$sce', function($scope, report, $sce){

            $scope.report_data = 'sale-inventory';
            $scope.from_date = new Date();
            $scope.to_date = new Date();
            $scope.data  = [];
            $scope.report_title =  '';
            $scope.report_src = '';
            console.log($scope.report_src);
            $scope.search = function(){
                if ( $scope.report_data == 'sale-inventory' ) {
                    $scope.report_src = '/sales/sale-inventory/'+ moment($scope.from_date).format('YYYY-MM-DD') + '/' + moment($scope.to_date).format('YYYY-MM-DD');
                } else if ( $scope.report_data == 'daily-sales' ) {
                    $scope.report_src = '/sales/daily-sales/'+ moment($scope.from_date).format('YYYY-MM-DD') + '/' + moment($scope.to_date).format('YYYY-MM-DD');
                } else if ( $scope.report_data == 'sales-transaction' ) {
                    $scope.report_src = '/sales/sales-transaction/'+ moment($scope.from_date).format('YYYY-MM-DD') + '/' + moment($scope.to_date).format('YYYY-MM-DD');
                } else if ( $scope.report_data == 'voided-sales' ) {
                    $scope.report_src = '/sales/voided-sales/'+ moment($scope.from_date).format('YYYY-MM-DD') + '/' + moment($scope.to_date).format('YYYY-MM-DD');
                }

            };

        }]);

} )();
