angular.module('SmartChurchPanel').controller('EsqueciSenhaCtrl', function ($scope, $state, $dialogs, $notifications, Public) { 
    
    
    $scope.dataFrm = {
        isValid: false,
        data: {
            email: { value: '', notEmpty: true, valid: true, StringfyFrom: '' }
        },
        toSend: {
            email: ''
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
            Public.requestNewPwd($scope.dataFrm.toSend.email).then(function() {
                $dialogs.onRequested('Sucesso!', 'Seu pedido de mundança de senha foi concluído com sucesso. '+
                                        'As instruções para continuar o processo foram enviadas para seu e-mail.').then(function() {
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


