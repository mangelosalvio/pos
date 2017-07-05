(function(){
    angular
        .module('cashier')
        .controller('TableController',[
            '$scope',
            '$routeParams',
            'tables',
            '$filter',
            'products',
            'pouchDB',
            '$mdDialog',
            '$http',
            'print',
            '$document',
            'auth',
            '$location',
            function($scope, $routeParams, tables, $filter, products, pouchDB, $mdDialog, $http, print, $document, auth, $location){
                $scope.user = {};
                /**
                 * get user
                 */
                auth.get()
                    .then(function(result){
                        if (result.data == null || result.data == ''){
                            window.location.href = "/";
                        } else {
                            $scope.user = result.data;

                            /**
                             * xread function
                             */

                            $scope.xread = function(){
                                $http.post('sales/xread',$scope.user)
                                    .then(function(result){
                                        $mdDialog.show(
                                            $mdDialog.alert()
                                                .content("<h2>Xread Printed</h2>")
                                                .title("Xread")
                                                .ok("Ok")
                                        );
                                    });
                            };

                            $scope.zread = function(){
                                $http.post('sales/zread',$scope.user)
                                    .then(function(result){
                                        if ( result.data == '' || result.data == null ){
                                            var dialog_content = "Unable to Zread. No Transactions";
                                        } else {
                                            var dialog_content = "Zread Printed";
                                        }

                                        $mdDialog.show(
                                            $mdDialog.alert()
                                                .content("<h2>"+ dialog_content +"</h2>")
                                                .title("Zread")
                                                .ok("Ok")
                                        );
                                    });
                            };
                        }

                    });

                var db = pouchDB('t1');
                var db_to = pouchDB('to');

                $scope.label = "Product Code";
                $scope.table_id = $routeParams.id;
                $scope.products = [];
                $scope.order = {};
                $scope.item = {};


                $scope.eval = "enterStockCode";
                $scope.category_id = 1;

                $('#input').focus();

                /**
                 * category selection
                 */
                $scope.selectCategory = function(category){
                    $scope.category_id = category.id;
                };



                /**
                 * Get Products
                 */
                products.getProducts()
                    .then(function(result){
                        $scope.products = result.data;
                    });

                /**
                 * if table_id is numeric get from db
                 * else db_to
                 */

                if ( !$.isNumeric($scope.table_id) ) {
                    db = db_to;
                }

                db.get($scope.table_id)
                    .then(function(result){
                        $scope.table = result;

                        $scope.ordersTotal = function(){
                            var total = 0;

                            angular.forEach($scope.table.orders, function(order){
                                angular.forEach(order.details, function(item) {
                                    total += item.amount;
                                });
                            });
                            return total;
                        };

                        $scope.reprintKitchen = function(order){
                            print.printOrderToKitchen(order)
                                .then(function(){
                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .content("<h2>Reprinted to kitchen.</h2>")
                                            .title("Reprint")
                                            .ok("Ok")
                                    );
                                });
                        };

                        $scope.enterCustomerName = function(){
                            $('#input').attr('disabled','disabled');
                            $mdDialog.show({
                                controller : CustomerNameController,
                                templateUrl : 'pages/dialog.customer.html',
                                clickOutsideToClose : false
                            }).then(function(result){
                                if ( result != '' && result != true && result != false ) {
                                    $scope.table.customer_name = result;
                                } else {
                                    $scope.table.customer_name = "";
                                }
                            }).finally(function(){
                                $scope.updateTableDB();
                                $('#input').removeAttr('disabled');
                                $('#input').focus();
                            });
                        };

                        $scope.doneOrder = function(){
                            if ( $scope.order.details ==  undefined ) {
                                return false;
                            }

                            if ( $scope.order.details.length <= 0 ){
                                return false;
                            }

                            /**
                             * ask user to print to kitchen
                             */

                            console.log($scope.table.customer_name);

                            if (  $scope.table.customer_name == '' || $scope.table.customer_name == null ) {
                                /**
                                 * if customer name is empty, ask for customer name
                                 * before done
                                 */

                                $('#input').attr('disabled','disabled');
                                $mdDialog.show({
                                    controller : CustomerNameController,
                                    templateUrl : 'pages/dialog.customer.html',
                                    clickOutsideToClose : false
                                }).then(function(result){
                                    if ( result != '' && result != true && result != false ) {
                                        $scope.table.customer_name = result;
                                    } else {
                                        $scope.table.customer_name = "";
                                    }

                                    /**
                                     * get kitchen remarks here
                                     */

                                    $('#input').attr('disabled','disabled');
                                    $mdDialog.show({
                                        controller : KitchenController,
                                        templateUrl : 'pages/dialog.kitchen.html',
                                        clickOutsideToClose : false
                                    }).then(function(remarks){
                                        remarks = ( remarks === true || remarks === false ) ? '' : remarks;
                                        $scope.order.remarks = remarks;

                                        if ( $scope.table.desc == undefined ||
                                            $scope.table.desc == '' ||
                                            $scope.table.desc == null){
                                            $scope.order.table_desc = "TAKE-OUT";
                                        } else {
                                            $scope.order.table_desc = $scope.table.desc;
                                        }
                                        $scope.order.customer_name = $scope.table.customer_name;

                                        print.printOrderToKitchen($scope.order)
                                            .then(function(result){

                                            });
                                    }).catch(function(remarks){
                                        remarks = ( remarks === true || remarks === false ) ? '' : remarks;
                                        $scope.order.remarks = remarks;
                                    }).finally(function(){
                                        $scope.table.orders.push($scope.order);
                                        $scope.order = {};
                                        $scope.updateTableDB();
                                        $('#input').removeAttr('disabled');
                                        $('#input').focus();
                                    });

                                    /**
                                     * end getting kitchen remarks here
                                     */
                                }).finally(function(){
                                    $scope.updateTableDB();
                                    $('#input').removeAttr('disabled');
                                    $('#input').focus();
                                });
                            }  else {
                                $('#input').attr('disabled','disabled');
                                $mdDialog.show({
                                    controller : KitchenController,
                                    templateUrl : 'pages/dialog.kitchen.html',
                                    clickOutsideToClose : false
                                }).then(function(remarks){
                                    remarks = ( remarks === true || remarks === false ) ? '' : remarks;
                                    $scope.order.remarks = remarks;

                                    if ( $scope.table.desc == undefined ||
                                        $scope.table.desc == '' ||
                                        $scope.table.desc == null){
                                        $scope.order.table_desc = "TAKE-OUT";
                                    } else {
                                        $scope.order.table_desc = $scope.table.desc;
                                    }
                                    $scope.order.customer_name = $scope.table.customer_name;

                                    print.printOrderToKitchen($scope.order)
                                        .then(function(result){

                                        });
                                }).catch(function(remarks){
                                    remarks = ( remarks === true || remarks === false ) ? '' : remarks;
                                    $scope.order.remarks = remarks;
                                }).finally(function(){
                                    $scope.table.orders.push($scope.order);
                                    $scope.order = {};
                                    $scope.updateTableDB();
                                    $('#input').removeAttr('disabled');
                                    $('#input').focus();
                                });
                            }
                        };

                        $scope.deleteOrder = function(order, orders){
                            var index = orders.indexOf(order);
                            orders.splice(index,1);
                            $scope.updateTableDB();

                            var cancelled_order = {};
                            cancelled_order.order = order;
                            cancelled_order.customer_name = $scope.table.customer_name;

                            if ( $scope.table.desc == undefined ||
                                $scope.table.desc == '' ||
                                $scope.table.desc == null){
                                cancelled_order.table_desc = "TAKE-OUT";
                            } else {
                                cancelled_order.table_desc = $scope.table.desc;
                            }

                            print.cancelOrder(cancelled_order)
                                .then(function(result){
                                    console.log(result);
                                });
                        };

                    })
                    .catch(function(error){
                        console.log(error);
                    });

                /**
                 * checks the item if in order detail,
                 * if item is in list, it will increment the item quantity
                 * then would return true
                 *
                 * if item is not found, it would return false
                 * @param item
                 */
                $scope.checkItemInOrderDetails = function(item){

                    var item_found = false;

                    angular.forEach($scope.order.details, function(product){
                        if ( product.id == item.id ) {
                            product.quantity = parseFloat(product.quantity) + parseFloat(item.quantity);
                            item_found = true;
                        }
                    });

                    return item_found;
                };

                $scope.addItemToOrder = function( product ){
                    var item = angular.copy(product);

                    if ( $.isNumeric($scope.input) ) {
                        item.quantity = $scope.input;
                    } else {
                        item.quantity = 1;
                    }

                    /**
                     * check if item is in list,
                     * if item is in list then increment item in list
                     */

                    if ( !$scope.checkItemInOrderDetails(item) ) {
                        $scope.initItem(item);
                        if ( $scope.order.details == undefined ) {
                            $scope.order.details = [];
                        }
                        $scope.order.details.push(item);
                    }

                    $scope.input = '';
                    $('#input').focus();
                };

                /**
                 * Provide fields for item
                 */
                $scope.initItem = function(item){
                    item.disc = 0;
                    item.amount = item.price * item.quantity;
                    item.vat_disc = 0;
                    item.vat_sales = parseFloat(numeral(item.amount / 1.12).format('0.00'));
                    item.vat_amount = item.amount - item.vat_sales;
                    item.vat_exempt = 0;
                };

                $scope.reset = function(){
                    $scope.item = {};
                    $scope.label = "Product Code"
                    $scope.input = '';
                    $scope.eval = 'enterStockCode';
                    $('#input').focus();
                };

                $scope.orderTotal = function(){
                    var total = 0;
                    angular.forEach($scope.order.details, function(item){
                        total += parseFloat(item.amount);
                    });
                    return total;
                };

                $scope.deleteItemInOrder = function(item){
                    var index = $scope.order.details.indexOf(item);
                    $scope.order.details.splice(index, 1);

                };

                $scope.deleteItemInOrders = function(item,order){
                    var index = order.details.indexOf(item);
                    order.details.splice(index, 1);

                    var cancelled_order = {};
                    cancelled_order.item = item;
                    cancelled_order.customer_name = $scope.table.customer_name;

                    if ( $scope.table.desc == undefined ||
                        $scope.table.desc == '' ||
                        $scope.table.desc == null){
                        cancelled_order.table_desc = "TAKE-OUT";
                    } else {
                        cancelled_order.table_desc = $scope.table.desc;
                    }

                    print.cancelItem(cancelled_order)
                        .then(function(result){
                            console.log(result);
                        });

                    $scope.updateTableDB();
                };

                $scope.total = function(order){
                    var total = 0;
                    angular.forEach(order.details, function(item) {
                        total += item.amount;
                    });

                    return total;
                };

                $scope.updateTableDB = function(){
                    db.put($scope.table)
                        .then(function(result){
                            if ( result.ok ) {
                                $scope.table._rev = result.rev;
                            }
                        })
                        .catch(function(error){
                            console.log(error);
                        });
                };

                $scope.finishTransaction = function(){
                    $('#input').attr('disabled','disabled');
                    $mdDialog.show({
                        controller : AmountController,
                        templateUrl : 'pages/dialog.amount.html',
                        clickOutsideToClose : false
                    }).then(function(result){
                        //$scope.updateTableDB();
                        $scope.checkCash(result);
                    }).finally(function(){
                        $('#input').removeAttr('disabled');
                    });
                };

                $scope.voidSale = function(){
                    $('#input').attr('disabled','disabled');
                    $mdDialog.show({
                        controller : VoidController,
                        templateUrl : 'pages/dialog.void.html',
                        clickOutsideToClose : false
                    }).then(function(result){
                        $http.post('/sales/void',result)
                            .then(function(result){
                                if ( result.data != '' ) {
                                    var dialog_content = "Sale Voided";
                                } else {
                                    var dialog_content = "Sale not voided";
                                }

                                $mdDialog.show(
                                    $mdDialog.alert()
                                        .content("<h1>"+dialog_content+"</h1>")
                                        .title("Void Sale")
                                        .ok("Ok")
                                    ).finally(function(){
                                        $scope.input = "";
                                        $('#input').focus();
                                    });
                            });
                    }).finally(function(){
                        $('#input').removeAttr('disabled');
                    });
                };


                $scope.keyup = function($event){
                    /**
                     * On Enter
                     */
                    if ( $event.keyCode == 27 ) {
                        $scope.reset();
                    } else if ( $event.keyCode == 121 ) {
                        /**
                         * f10
                         */
                        $event.preventDefault();
                        $scope.finishTransaction();
                    } else if ( $event.keyCode == 120 ){
                        /**
                         * f9
                         */
                        $scope.printBill();
                    } else if ( $event.keyCode == 119 ) {
                        /**
                         * f8
                         */
                        $scope.applyDiscount();
                    } else if ( $event.keyCode == 118 ) {
                        /**
                         * f7
                         */
                        $scope.doneOrder();
                    } else if ( $event.keyCode == 114 ) {
                        /**
                         * f3
                         */
                        $scope.clearOrders();
                    } else if ( $event.keyCode == 13 ) {
                        /**
                         * enter
                         * get stock code here
                         */
                        var arr = $scope.input.split('*');
                        if ( arr.length > 1 ) {
                            if ( $.isNumeric( arr[0] ) ) {
                                var quantity = arr[0];
                                var stock_code = arr[1];
                            } else {
                                var quantity = arr[1];
                                var stock_code = arr[0];
                            }
                        } else {
                            var stock_code = $scope.input;
                            var quantity = 1;
                        }

                        if ( $scope.eval == "enterStockCode" ) {
                            products.getProductFromCode(stock_code)
                                .then(function(result){
                                    if ( result.data != '' ) {

                                        $scope.item = result.data;
                                        $scope.item.quantity = quantity;
                                        $scope.input = "";

                                        if ( !$scope.checkItemInOrderDetails($scope.item) ) {
                                            $scope.initItem($scope.item);
                                            if ( $scope.order.details == undefined ) {
                                                $scope.order.details = [];
                                            }
                                            $scope.order.details.push($scope.item);
                                        }

                                        $scope.reset();

                                    }
                                });
                        }


                    }
                };

                $scope.printBill = function(){
                    $mdDialog.show(
                        $mdDialog.confirm()
                            .title('Bill')
                            .content('<h2>Would you like to print to bill?</h2>')
                            .ok('Print')
                            .cancel('No')
                    ).then(function(){

                            if ( $scope.table.desc == undefined ||
                                $scope.table.desc == '' ||
                                $scope.table.desc == null){
                                $scope.table.desc = "TAKE-OUT";
                            }

                            print.printBill($scope.table)
                                .then(function(result){
                                    console.log(result.data);
                                })
                                .finally(function(){
                                    $('#input').focus();
                                });
                            $('#input').focus();
                        });
                };

                $scope.reprintInvoice = function(){
                  print.reprintInvoice()
                      .then(function(result){
                          $mdDialog.show(
                              $mdDialog.alert()
                                  .content("<h2>Reprinted Invoice.</h2>")
                                  .title("Reprint")
                                  .ok("Ok")
                          );
                      });
                };

                $scope.checkCash = function(amount){
                    /**
                     * Cash Amount greater than orders total
                     */
                    if ( $scope.ordersTotal() > amount ) {
                        $mdDialog.show(
                            $mdDialog.alert()
                                .content("Invalid Amount")
                                .title("Error")
                                .ok("Ok")
                        ).then(function (result) {
                                $scope.input = "";
                                $('#input').focus();
                            });
                    }else if ( $scope.table.orders.length <= 0 ){
                        $('#input').attr('disabled','disabled');
                        $mdDialog.show(
                            $mdDialog.alert()
                                .content("<h2>No Items to Process</h2>")
                                .title("Error")
                                .ok("Ok")
                        ).then(function(result){
                                $('#input').removeAttr('disabled');
                                $scope.input = "";
                                $('#input').focus();
                            });
                    } else {
                        /**
                         * send items to server
                         */
                        $scope.table.cash_amount = amount;
                        $scope.table.change_amount = $scope.table.cash_amount - $scope.ordersTotal();
                        $scope.table.user = $scope.user;
                        $http.post('/sales', $scope.table)
                            .then(function(result){
                                $mdDialog.show(
                                    $mdDialog.alert()
                                        .title("Change")
                                        .content( "<h1 style='text-align:center;'>" + numeral($scope.table.change_amount).format('0,0.00') + "</h1>")
                                        .ok("Ok")
                                    ).then(function(result){
                                        $scope.input = "";
                                        $('#input').focus();
                                    }).finally(function(){
                                        $scope.label = "Product Code";
                                        $scope.eval = "enterStockCode";
                                        $scope.table.orders = [];
                                        $scope.order = {};
                                        $scope.table.cash_amount = 0;
                                        $scope.table.change_amount = 0;
                                        $scope.input = "";
                                        $scope.updateTableDB();

                                        /**
                                         * reset discount here
                                         */
                                        $scope.table.discount = {};
                                        $scope.table.discount.disc_type = 'no_discount';
                                        $scope.table.discount.senior  = {};
                                        $scope.table.discount.senior.disc_type = 'dine';
                                        $scope.table.discount.senior.no_of_seniors = 1;
                                        $scope.table.discount.senior.no_of_persons = 1;
                                        $scope.table.discount.senior.dine_no_of_seniors = 1;
                                        $scope.table.select_all = false;

                                        /**
                                         * reset customer name here
                                         */

                                        $scope.table.customer_name = "";


                                        /**
                                         * if take-out remove doc
                                         */


                                        if ( !$.isNumeric($scope.table_id) ) {
                                            console.log($scope.table);
                                            console.log($scope.table._id);
                                            console.log($scope.table._rev);

                                            db_to.remove($scope.table._id, $scope.table._rev)
                                                .then(function(result){
                                                    console.log(result)
                                                    $location.path('/');
                                                }).catch(function(err){
                                                    /**
                                                     * if error, try again
                                                     */
                                                    db_to.remove($scope.table._id, $scope.table._rev)
                                                        .then(function(result){
                                                            $location.path('/');
                                                        });
                                                });
                                        }


                                    });

                            });
                    }
                };

                $scope.applyDiscount = function(){

                    $mdDialog.show({
                        controller : DiscountController,
                        templateUrl : 'pages/dialog.discount.html',
                        clickOutsideToClose : false,
                        table : $scope.table
                    }).then(function(result){
                        $scope.updateTableDB();
                        $('#input').focus();
                    });
                };

                $scope.clearOrders = function(){
                    $mdDialog.show(
                        $mdDialog.confirm()
                            .title('Clear orders')
                            .content('<h2>Would you like to clear orders of this table?</h2>')
                            .ok('Clear')
                            .cancel('Cancel')
                        )
                        .then(function(){
                            $scope.order = {};
                            $scope.table.orders = [];
                            $scope.updateTableDB();

                            /**
                             * delete contents if takout
                             */

                            if ( $scope.table.desc == undefined ) {
                                db_to.remove($scope.table._id, $scope.table._rev)
                                    .then(function (result) {
                                        console.log(result)
                                        $location.path('/');
                                    }).catch(function (err) {
                                        /**
                                         * if error, try again
                                         */
                                        db_to.remove($scope.table._id, $scope.table._rev)
                                            .then(function (result) {
                                                $location.path('/');
                                            });
                                    });
                            }


                            /**
                             * end of delete takeout
                             */

                        })
                        .finally(function(){
                            $('#input').focus();
                        });
                };

            }]);

    function AmountController($scope, $mdDialog){
        $scope.done = function() {
            $mdDialog.hide($scope.amount);
        };
    }

    function CustomerNameController($scope, $mdDialog){
        $scope.done = function() {
            $mdDialog.hide($scope.input);
        };
    }

    function VoidController($scope, $mdDialog){
        $scope.done = function() {
            $mdDialog.hide($scope.input);
        };
    }

    function KitchenController($scope, $mdDialog){
        $scope.remarks = '';
        $scope.cancel = function() {
            $mdDialog.cancel($scope.remarks);
        };
        $scope.done = function() {
            $mdDialog.hide($scope.remarks);
        };
    }

    function DiscountController($scope, $mdDialog, table){
        $scope.orders = table.orders;
        if ( table.discount == undefined ) {
            table.discount = {};
            table.discount.disc_type = 'no_discount';
            table.discount.senior  = {};
            table.discount.senior.disc_type = 'dine';
            table.discount.senior.no_of_seniors = 1;
            table.discount.senior.no_of_persons = 1;
            table.discount.senior.dine_no_of_seniors = 1;
            table.select_all = false;
        }
        $scope.discount = table.discount;


        $scope.done = function(){

            if ( parseFloat($scope.discount.senior.no_of_seniors) > parseFloat($scope.discount.senior.no_of_persons) &&
                $scope.discount.disc_type == 'senior' && $scope.discount.senior.disc_type == 'carry' ) {
                $mdDialog.hide();

                $mdDialog.show(
                    $mdDialog.alert()
                        .content("Invalid No of Senior/Person Input")
                        .title("Error")
                        .ok("Ok")
                );
                return false;
            }

            angular.forEach($scope.orders, function(order){
                angular.forEach(order.details, function(item){

                    /**
                     * check amount of discount
                     */

                    if ( $scope.discount.disc_type == 'senior' ) {

                        if ( $scope.discount.senior.disc_type == 'dine' ) {
                            /**
                             * Dine in
                             */
                            $scope.seniorDineDiscount();

                        } else {
                            /**
                             * Take-out / Carry
                             */
                            $scope.seniorCarryDiscount();

                        }
                    } else if ( $scope.discount.disc_type == 'pwd' ) {
                        /**
                         * 20% discount
                         */
                        if ( item.is_disc_applied ) {
                            item.vat_disc = 0;
                            item.disc = item.price * item.quantity * .20;
                            item.amount = item.price * item.quantity - item.disc;
                        } else {
                            item.disc = 0;
                            item.vat_disc = 0;
                            item.amount = item.price * item.quantity;
                        }

                    } else {
                        /**
                         * no discount
                         */
                        item.disc = 0;
                        item.vat_disc = 0;
                        item.amount = item.price * item.quantity - item.disc;
                    }
                });
            });
            $mdDialog.hide();
        };

        $scope.seniorCarryDiscount = function(){
            angular.forEach($scope.orders, function(order){
                angular.forEach(order.details, function(item){
                    if ( item.is_disc_applied ) {
                        var gross_amount = item.price * item.quantity;
                        var senior_applied_amount = parseFloat(numeral(gross_amount * $scope.discount.senior.no_of_seniors / $scope.discount.senior.no_of_persons).format('0.00'));
                        var normal_applied_amount = numeral(gross_amount * ($scope.discount.senior.no_of_persons -  $scope.discount.senior.no_of_seniors) / $scope.discount.senior.no_of_persons).format('0.00');
                        item.vat_sales = parseFloat(numeral(normal_applied_amount / 1.12).format('0.00'));
                        item.vat_amount = normal_applied_amount - item.vat_sales;
                        item.vat_exempt = senior_applied_amount;
                        item.vat_disc = parseFloat(numeral(senior_applied_amount / 1.12 * .12).format('0.00'));
                        item.disc = parseFloat(numeral((senior_applied_amount - item.vat_disc) * 0.20).format('0.00'));
                        item.amount = gross_amount - item.vat_disc - item.disc;

                    } else {
                        item.disc = 0;
                        item.amount = item.price * item.quantity - item.disc;
                    }
                });
            });
        };

        $scope.seniorDineDiscount = function(){
            angular.forEach($scope.orders, function(order){
                angular.forEach(order.details, function(item){
                    if ( item.is_disc_applied ) {

                        var no_of_persons = parseFloat(item.serving_size);
                        var no_of_seniors = parseFloat($scope.discount.senior.dine_no_of_seniors);
                    
                        if ( no_of_seniors > no_of_persons ) {
                            no_of_seniors = no_of_persons;                            
                        }

                        

                        var gross_amount = item.price * item.quantity;
                        var senior_applied_amount = parseFloat(numeral(gross_amount * no_of_seniors / no_of_persons).format('0.00'));
                        var normal_applied_amount = numeral(gross_amount * (no_of_persons -  no_of_seniors) / no_of_persons).format('0.00');

                        item.vat_sales = parseFloat(numeral(normal_applied_amount / 1.12).format('0.00'));
                        item.vat_amount = normal_applied_amount - item.vat_sales;
                        item.vat_exempt = senior_applied_amount;
                        item.vat_disc = parseFloat(numeral(senior_applied_amount / 1.12 * .12).format('0.00'));
                        item.disc = parseFloat(numeral((senior_applied_amount - item.vat_disc) * 0.20).format('0.00'));
                        item.amount = gross_amount - item.vat_disc - item.disc;

                    } else {
                        item.disc = 0;
                        item.amount = item.price * item.quantity - item.disc;
                    }
                });
            });
        };

        $scope.selectAll = function(){
            angular.forEach($scope.orders, function(order){
                angular.forEach(order.details, function(item){
                    if ( $scope.select_all ) {
                        item.is_disc_applied = true;
                    } else {
                        item.is_disc_applied = false;
                    }
                });
            });
        };

        $scope.applyDiscount = function(){
            $scope.select_all = false;
        };

    }

})();