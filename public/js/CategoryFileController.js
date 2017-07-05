(function(){
    angular
        .module('cashier')
        .controller('CategoryFileController',['$scope','category',
            function($scope,category){
                $scope.categories = [];
                $scope.category = {};

                category.getItems().then(function(result){
                    $scope.categories = result.data;
                });

                $scope.save = function(){
                    category.add($scope.category)
                        .then(function(result){
                            $scope.categories = result.data;
                        });
                    $scope.category = {};
                    $("#category_desc").focus();
                };

                $scope.edit = function(category){
                    $scope.category = category;
                };

                $scope.delete = function(item){
                    category.delete(item)
                        .then(function(result){
                            $scope.categories = result.data;
                        });
                };
             }]);
})();