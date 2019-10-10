var theApp = angular.module('theApp', []);
theApp.controller('filesCtl', function($scope, $http, $element, $timeout, $window) {
    $scope.downloadFile = function(event, id) {
        var trItem = $element.find('#tr-' + id);
        var path = trItem.attr("data-href");
        var file = trItem.attr("data-file");
        var url = window.location.href;
        url += (url.search('/?') > 0 ? '&' : '?');

        $window.open(url + "act=download&targetf=" + file, '_blank');
        $window.focus();
    }
    $scope.zipDir = function(event, id) {
        var zipBtn = angular.element(event.target);
        zipBtn.html('<span class="spinner-grow spinner-grow-sm" style="margin:0 5px 0 -2px;" role="status" aria-hidden="true"></span> Zipping ...');
        angular.element(".btn-dirtozip").attr("disabled", true);

        var trItem = $element.find('#tr-' + id);
        var path = trItem.attr("data-href");

        $http({
            method: 'POST',
            url: "index.php",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: {
                act: 'zip',
                path: path
            }
        }).then(function successCallback(response) {
            if (response.data.status === true) {
                zipBtn.html('Create Zip File');
                angular.element(".btn-dirtozip").attr("disabled", false);

                $timeout(function() {
                    $window.location.reload();
                }, 250);
            } else {
                trItem.addClass("dangerHighlight");
                $timeout(function() {
                    trItem.addClass("highlightOut");
                }, 1000);
            }

        }, function errorCallback(response) {});
    }

    $scope.deleteFileDir = function(event, id) {
        var trItem = $element.find('#tr-' + id);
        var path = trItem.attr("data-href");
        trItem.removeClass();
        $http({
            method: 'DELETE',
            url: "index.php",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: {
                act: 'del',
                path: path
            }
        }).then(function successCallback(response) {
            if (response.data.status === true) {
                trItem.hide(250);
                $timeout(function() {
                    trItem.remove();
                }, 250);
            } else {
                trItem.addClass("dangerHighlight");
                $timeout(function() {
                    trItem.addClass("highlightOut");
                }, 1000);
            }

        }, function errorCallback(response) {});
    }

});


var addNewFileStatus = true; // false if addNewFolder is active

theApp.controller('addNewCtl', function($scope, $http, $element, $timeout, $window) {
    $scope.showUploadBox = function(event, id) {
        var btn = angular.element(event.target);
            btn.addClass("active");
        var uploadBox = angular.element('#filedrop');
        if (uploadBox.is(':visible')) {
            btn.removeClass("active");
            uploadBox.hide(250);
        } else {
            btn.addClass("active");
            uploadBox.show(250);
        }
    }        
    $scope.addNewFile = function(event, id) {
        var btn = angular.element(event.target);

        var input = $element.find('#newfileName');
        if (btn.hasClass('active')) {
            btn.parent().find("li").not(':first').removeClass();
            input.parent().find('input').hide(250);
        } else {
            btn.parent().find("li").not(':first').removeClass();
            btn.addClass("active");
            input.parent().find('input').show(250);
        }
        input.attr('placeholder', 'File Name');
        
        addNewFileStatus = true;
    }
    $scope.addNewFolder = function(event, id) {
        var btn = angular.element(event.target);

        var input = $element.find('#newfileName');
        if (btn.hasClass('active')) {
            btn.parent().find("li").not(':first').removeClass();
            input.parent().find('input').hide(250);
        } else {
            btn.parent().find("li").not(':first').removeClass();
            btn.addClass("active");
            input.parent().find('input').show(250);
        }
        input.attr('placeholder', 'Folder Name');

        addNewFileStatus = false;
    }
    $scope.addNewFileFolder = function(event, id) {
        var input = $element.find('#newfileName');
        $scope.cdata = {};
        $scope.cdata.action = 'addFile';
        if (!addNewFileStatus) {
            $scope.cdata.action = 'addFolder';
        }
        
        $scope.cdata.name = input.val();
        if ($scope.cdata.name.length <= 0) {
            input.addClass("dangerHighlight");
            $timeout(function() {
                input.addClass("highlightOut");
            }, 1000);
        } else {
            $http({
                method: 'POST',
                url: "index.php",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: {
                    act: $scope.cdata.action,
                    dir: directory,
                    name: $scope.cdata.name
                }
            }).then(function successCallback(response) {
                console.log(response.data);
                if (response.data.status === true) {
                    $window.location.reload();
                } else {
                    input.addClass("dangerHighlight");
                    $timeout(function() {
                        input.addClass("highlightOut");
                    }, 1000);
                }
            }, function errorCallback(response) {
                input.addClass("dangerHighlight");
                $timeout(function() {
                    input.addClass("highlightOut");
                }, 1000);
            });
        }
    }
    $scope.addNewFileFolderHide = function(event, id) {
        var btn = angular.element(event.target);
        btn.parent().parent().find("li").not(':first').removeClass();
        btn.parent().find("input").hide(250);
    }

});


theApp.directive('upload', ['$http', '$window', function($http, $window) {
    return {
        restrict: 'E',
        replace: true,
        scope: {},
        require: '?ngModel',
        template: '<div class="asset-upload" id="filedrop">Drag here to upload</div>',
        link: function(scope, element, attrs, ngModel) {
            var upload = function(files) {
                var data = new FormData();
                var index = 0;
                angular.forEach(files, function(value) {
                    data.append("files" + (index++), value);
                });
                data.append("upload", 1);
                data.append("path", directory);

                $http({
                    method: 'POST',
                    url: attrs.to,
                    data: data,
                    withCredentials: true,
                    headers: {
                        'Content-Type': undefined
                    },
                    transformRequest: angular.identity
                }).then(function successCallback(response) {
                    if (response.data.status === true) {
                        $window.location.reload();
                    } else {}
                }, function errorCallback(response) {

                });
            };
            // Code goes here
            element.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            element.on('dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });
            element.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (e.originalEvent.dataTransfer) {
                    if (e.originalEvent.dataTransfer.files.length > 0) {
                        upload(e.originalEvent.dataTransfer.files);
                    }
                }
                return false;
            });
        }
    };
}]);