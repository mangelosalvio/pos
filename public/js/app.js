( function(){
    angular
        .module('cashier',['ngMaterial','ngMessages','ngRoute','pouchdb', 'angularUtils.directives.dirPagination'])
        .config(['$routeProvider','paginationTemplateProvider', function($routeProvider){
            $routeProvider.
                when('/',{
                    templateUrl: 'pages/tables.html',
                    controller : 'mainController'
                }).
                when('/table/:id', {
                    templateUrl: 'pages/table.html',
                    controller : 'TableController'
                }).
                when('/products', {
                    templateUrl: 'pages/products.html',
                    controller : 'ProductsFileController'
                }).
                when('/category', {
                    templateUrl: 'pages/category.html',
                    controller : 'CategoryFileController'
                }).
                when('/tablefile', {
                    templateUrl: 'pages/tablefile.html',
                    controller : 'TableFileController'
                }).
                when('/sale-inventory', {
                    templateUrl: 'pages/sale_inventory.html',
                    controller : 'SaleInventoryController'
                }).
                when('/users', {
                    templateUrl: 'pages/users.html',
                    controller : 'UserFileController'
                }).
                when('/sales', {
                    templateUrl: 'pages/sales.html',
                    controller : 'SalesFileController'
                }).
                otherwise({
                    redirectTo: '/'
                });
        }])
        .config(['$mdDateLocaleProvider', function($mdDateLocaleProvider){
            $mdDateLocaleProvider.formatDate = function(date) {
                return moment(date).format('YYYY-MM-DD');
            };

            $mdDateLocaleProvider.parseDate = function(dateString) {
                return moment(dateString).toDate();
            };
        }])
        .factory("tables",function($http){
            var data = [];
            var tables = {};

            tables.getTables = function(){
                return $http.get('/tables');
            };

            tables.getData = function(){
                return data;
            };

            tables.setData = function(d){
                data = d;
            };

            tables.postTableInfo = function(data){
                return $http.post('/tables',data);
            };

            tables.delete = function(data){
                return $http.delete('/tables/' + data.id);
            };

            return tables;
        })
        .factory("products",function($http){
            var products = {};

            products.getProductFromCode = function(stock_code) {
                return $http.get('/products/code/' + stock_code);
            };

            products.getProduct = function(id) {
                return $http.get('/products/' + id);
            };

            products.getProducts = function(){
                return $http.get('/products');
            };

            products.addProduct = function(data){
                return $http.post('/products',data);
            };

            products.delete = function(data){
                return $http.delete('/products/' + data.id);
            };

            products.deleteSubItem = function(product_id, item_id){
                return $http.delete('/products/' + product_id + '/' + item_id);
            };

            return products;
        })
        .factory("category",function($http){
            var obj = {};

            obj.get = function(id) {
                return $http.get('/category/' + id);
            };

            obj.getItems = function(){
                return $http.get('/category');
            };

            obj.add = function(data){
                return $http.post('/category',data);
            };

            obj.delete = function(data){
                return $http.delete('/category/' + data.id);
            };

            return obj;
        })
        .factory('print', function($http){
            var print = {};

            print.printOrderToKitchen  = function(order){
                return $http.post('/order',order);
            };

            print.printBill = function(table){
                return $http.post('/order/bill',table);
            };

            print.reprintInvoice = function(){
                return $http.get('/sales/reprintInvoice');
            };

            print.cancelItem = function(item){
                return $http.post('/order/cancelItem',item);
            };

            print.cancelOrder = function(order){
                return $http.post('/order/cancelOrder', order);
            };

            return print;
        })
        .factory('auth', function($http){
            var obj = {};

            obj.get = function(){
                return $http.get('/user');
            };

            return obj;
        })
        .factory("sales",function($http){
            var sales = {};

            sales.getSale = function(id) {
                return $http.get('/salesfile/' + id);
            };

            sales.getSales = function(pageNumber, search){
                /*return $http.get('/salesfile/' + pageNumber + '/page');*/
                /*return $http.get('/salesfile/page/' + pageNumber );*/
                return $http.get('/salesfile?page=' + pageNumber + '&search=' + search );
            };

            sales.addProduct = function(data){
                return $http.post('/salesfile',data);
            };

            sales.delete = function(data){
                return $http.delete('/salesfile/' + data.id);
            };

            sales.deleteSubItem = function(product_id, item_id){
                return $http.delete('/salesfile/' + product_id + '/' + item_id);
            };

            return sales;
        });



} )();