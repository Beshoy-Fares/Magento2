/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

'use strict';
angular.module('component-update-success', ['ngStorage'])
    .controller('componentUpdateSuccessController', ['$scope', '$state', '$localStorage', '$window', function ($scope, $state, $localStorage, $window) {
        if ($localStorage.packages) {
            $scope.packages = $localStorage.packages;
        }
        $scope.back = function () {
            $window.location.href = '';
        }
    }]);
