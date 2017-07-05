(function(){
    angular
        .module('cashier')
        .controller('ProductsFileController',[
            '$scope',
            'products',
            'category',
            function($scope,products,category){
                $scope.products = [];
                $scope.categories = [];
                $scope.product = {};

                category.getItems()
                    .then(function(result){
                        $scope.categories = result.data;
                    });

                products.getProducts().then(function(result){
                    $scope.products = result.data;
                });

                $scope.add = function(){
                    products.getProduct($scope.product.sub_item_id)
                        .then(function(result){
                            ($scope.product.sub_items = $scope.product.sub_items ? $scope.product.sub_items : []).push(result.data);
                        });
                };

                $scope.deleteSubItem = function(product, item){
                    if ( product != undefined ) {
                        products.deleteSubItem(product.id, item.id);

                        var index = product.sub_items.indexOf(item);
                        product.sub_items.splice(index, 1);
                    }

                };

                $scope.save = function(){

                    products.addProduct($scope.product)
                        .then(function(result){
                            $scope.products = result.data;
                        });

                    $scope.product = {};

                    $("#product_desc").focus();
                };

                $scope.editProduct = function(product){
                    $scope.product = product;
                };

                $scope.delete = function(product){
                    products.delete(product)
                        .then(function(result){
                            $scope.products = result.data;
                        });
                };

            }]);
})();