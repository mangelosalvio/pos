(function(){
    angular
        .module('cashier')
        .controller('mainController',['$scope','tables', 'pouchDB', '$log','$http', '$location', 'category', 'auth', '$mdDialog',
            function($scope, tables, pouchDB, $log, $http, $location, category, auth, $mdDialog) {

                $scope.categories = [];
                category.getItems()
                    .then(function(result){
                        $scope.categories = result.data;
                    });

                var db = pouchDB('t1');
                var db_to = pouchDB('to');
                $scope.tables = [];
                $scope.takeouts = [];

                /**
                 * check if there are any takeouts
                 */

                db_to.allDocs({
                    include_docs: true,
                    attachments: true
                }).then(function (result) {
                    // handle result
                    if ( result.rows.length > 0 ) {
                        angular.forEach(result.rows, function(row){
                            $scope.takeouts.push(row.doc);
                            //db_to.remove(row.doc);
                        });
                    }

                }).catch(function (err) {
                    console.log(err);
                });

                $scope.addTakeOut = function(){
                    $scope.takeOut = {};
                    $mdDialog.show({
                        controller : CustomerNameController,
                        templateUrl : 'pages/dialog.customer.html',
                        clickOutsideToClose : false
                    }).then(function(result){
                        $scope.takeOut.orders = [];

                        if ( result != '' && result != true && result != false ) {
                            $scope.takeOut.customer_name = result;
                        } else {
                            $scope.takeOut.customer_name = "";
                        }

                        /**
                         * create takout here
                         */

                        db_to.post($scope.takeOut)
                            .then(function(response){
                                console.log(response);
                                if ( response.ok ) {
                                    $scope.takeOut._id = response.id;
                                    $scope.takeOut._rev = response.rev;

                                    $scope.takeouts.push($scope.takeOut);
                                    console.log(response.id);
                                    $location.path('/table/'+response.id);
                                }
                            })
                            .catch(function(err){
                                console.log(err);
                            });
                    }).finally(function(){

                    });
                };

                /**
                 * check if there is a local table data existing
                 */

                tables.getTables()
                    .success(function(result){
                        var t = [];
                        angular.forEach(result, function(e,i){
                            if(e.deleted_at != null ) return false;

                            db.get(e.id.toString()).then(function(doc){
                                doc.desc = e.table_desc;
                                t.push(doc);

                                tables.setData(t);
                                $scope.tables = tables.getData();
                            }).catch(function(error){
                                /**
                                 * if id is not found, create a new table in db
                                 */
                                if ( error.status == 404 ) {
                                    var data = {
                                        _id : e.id.toString(),
                                        desc : e.table_desc,
                                        orders : []
                                    };
                                    db.put(data)
                                        .then(function(result){
                                            if ( result.ok ) {
                                                data._rev = result.rev;
                                            }
                                            t.push(data);
                                        })
                                        .catch(function(error){
                                            console.log(error);
                                        });

                                }
                            });
                        });
                    })
                    .error(function(error){
                        console.log(error);
                    });

                $scope.tableStatus = function (table) {
                    if ( table.orders == undefined ){
                        return "vacant";
                    }
                    if (table.orders.length <= 0) {
                        return "vacant";
                    } else {
                        return "taken";
                    }
                };

                $scope.link = function(url){
                    $location.url("/" + url);
                };

            }]);

    function CustomerNameController($scope, $mdDialog){
        $scope.done = function() {
            $mdDialog.hide($scope.input);
        };
    }
})();