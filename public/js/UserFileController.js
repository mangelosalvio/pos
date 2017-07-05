(function(){
    angular
        .module('cashier')
        .factory('roles', function($http){
            var obj = {};

            obj.getItems = function() {
                return $http.get('/roles');
            };

            obj.get = function(id) {
                return $http.get('/roles/' + id);
            };

            obj.add = function(data){
                return $http.post('/roles',data);
            };

            obj.delete = function(data){
                return $http.delete('/roles/' + data.id);
            };

            return obj;
        })
        .factory('authUser', function($http){
            var obj = {};

            obj.getItems = function() {
                return $http.get('/users');
            };

            obj.get = function(id) {
                return $http.get('/users/' + id);
            };


            obj.add = function(data){
                return $http.post('/users',data);
            };

            obj.update = function(data){
                return $http.put('/users/' + data.id, data);
            };

            obj.delete = function(data){
                return $http.delete('/users/' + data.id);
            };

            return obj;
        })
        .controller('UserFileController',[
            '$scope',
            'roles',
            'authUser',
            '$mdDialog',
            function($scope,roles, authUser, $mdDialog){
                $scope.user = {};
                $scope.users = [];
                $scope.roles = [];
                $scope.errors = [];
                $scope.update_password = false;

                roles.getItems()
                    .then(function(result){
                        $scope.roles = result.data;
                    });


                authUser.getItems().then(function(result){
                    $scope.users = result.data;
                });

                $scope.save = function(){

                    if ( $scope.user.id == null ) {
                        /**
                         * save
                         */
                        authUser.add($scope.user)
                            .then(function(result){
                                /**
                                 * check errors
                                 */

                                if ( result.data.length > 0 ) {
                                    console.log(result.data.length);
                                    $scope.errors = result.data;
                                    console.log($scope.errors);
                                } else {
                                    $scope.user = {};
                                }

                                authUser.getItems().then(function(result){
                                    $scope.users = result.data;

                                });
                            });
                    } else {

                        /**
                         * update
                         */

                        authUser.update($scope.user)
                            .then(function(result){
                                /**
                                 * check errors
                                 */

                                if ( result.data.length > 0 ) {
                                    console.log(result.data.length);
                                    $scope.errors = result.data;
                                    console.log($scope.errors);
                                } else {
                                    $scope.user = {};


                                    $scope.errors = [];

                                    if ( $scope.update_password ) {
                                        var dialog_content = "Password Updated";
                                    } else {
                                        var dialog_content = "Information Updated";
                                    }

                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .content("<h2>"+ dialog_content +"</h2>")
                                            .title("User Account")
                                            .ok("Ok")
                                        ).then(function(){
                                        $('#fullname').focus();
                                        }).finally(function(){
                                            $scope.update_password = false;
                                        });
                                }

                                authUser.getItems().then(function(result){
                                    $scope.users = result.data;
                                });
                            });
                    }

                    $("#name").focus();
                };

                $scope.edit = function(user){
                    $scope.update_password = false;
                    user.name = null;
                    $scope.user = user;
                    $scope.user.role_id = user.roles[0].id;
                };

                $scope.delete = function(user){
                    authUser.delete(user)
                        .then(function(result){
                            $scope.users = result.data;
                        });
                };

                $scope.updatePassword = function(user){
                    user.name = null;
                    $scope.user = user;
                    $scope.user.role_id = user.roles[0].id;
                    $scope.update_password = true;
                    $('#password').focus();
                }

            }]);
})();