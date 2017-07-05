( function(){
    angular
        .module('cashier')
        .controller('TableFileController' ,['$scope','tables', function($scope,tables){
            $scope.tables = [];
            $scope.table = {};

            tables.getTables().then(function(result){
                $scope.tables = result.data;
            });

            $scope.add = function(){

                tables.postTableInfo($scope.table)
                    .then(function(result){
                        $scope.tables = result.data;
                    });

                $scope.table = {};

                $("#table_desc").focus();
            };

            $scope.edit = function(table){
                $scope.table = table;
            };

            $scope.delete = function(table){
                tables.delete(table)
                    .then(function(result){
                        $scope.tables = result.data;
                    });
            };
        }]);
} )();
