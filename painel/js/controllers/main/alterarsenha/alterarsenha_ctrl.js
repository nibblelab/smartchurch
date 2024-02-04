angular.module('SmartChurchPanel').controller('AlterarSenhaCtrl', function ($scope, $state, $dialogs, $notifications, $location, Public) { 
    
    $scope.authcode = $location.search().authcode; 
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            senha: { value: '', notEmpty: true, valid: true, StringfyFrom: '' }
        },
        toSend: {
            codigo: $scope.authcode,
            senha: ''
        },
        validate: function() {
            var valid = true;
            for (var k in $scope.dataFrm.data) {
                if ($scope.dataFrm.data[k].notEmpty) {
                    if ($scope.dataFrm.data[k].value == '') {
                        valid = false;
                        $scope.dataFrm.data[k].valid = false;
                    }
                }
            }
            
            $scope.dataFrm.isValid = valid;
            
            $scope.$apply();
        },
        prepare: function() {
            for (var k in $scope.dataFrm.data) {
                if ($scope.dataFrm.data[k].StringfyFrom != '') {
                    $scope.dataFrm.data[k].value = JSON.stringify($scope.dataFrm.data[$scope.dataFrm.data[k].StringfyFrom].value);
                }
                if ($scope.dataFrm.toSend.hasOwnProperty(k)) {
                    $scope.dataFrm.toSend[k] = $scope.dataFrm.data[k].value;
                }
            }
        }
    };
    
    $scope.showPass = false;
    $scope.showPassOrHide = function() {
        $scope.showPass = !$scope.showPass;
    };
    
    $('#frm').validate({
        submit: {
            settings: {
                inputContainer: '.form-group',
                errorListClass: 'form-control-error',
                errorClass: 'has-danger'
            },
            callback: {
                onSubmit: function(node, formData) {
                    $scope.doRequest();
                }
            }
        }
    });
    
    $scope.doRequest = function() {
        $scope.dataFrm.validate();
        if ($scope.dataFrm.isValid) {
            $scope.dataFrm.prepare();
            Public.resetPwd($scope.dataFrm.toSend).then(function() {
                $dialogs.onRequested('Sucesso!', 'Sua senha foi alterada com sucesso.').then(function() {
                    $state.go('login');
                });
            }, function(e) {
                $notifications.err(e);
            });
        } else {
            $notifications.err("Há campos incorretos!");
        }
    };
    
});


