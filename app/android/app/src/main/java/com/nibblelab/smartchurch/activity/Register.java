package com.nibblelab.smartchurch.activity;


import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.nibblelab.smartchurch.API.UserAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DialogHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.RegisterData;

public class Register extends Base {

    public static final String TAG = "Register";

    Button register_btn;
    EditText nome;
    EditText email;
    EditText senha;
    CheckBox termosCheck;
    TextView termosLnk;
    FloatingActionButton back;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        /* config */
        nome = (EditText) findViewById(R.id.reg_nome_fld);
        email = (EditText) findViewById(R.id.reg_email_fld);
        senha = (EditText) findViewById(R.id.reg_senha_fld);
        register_btn = (Button) findViewById(R.id.reg_btn);
        termosCheck = (CheckBox) findViewById(R.id.checkbox_termos);
        termosLnk = (TextView) findViewById(R.id.termos_lnk);
        back = (FloatingActionButton) findViewById(R.id.reg_back);
        progress = (ProgressBar) findViewById(R.id.reg_progress);

        this.initLoadingSpinner();

        /* botão de registro */
        register_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Register.this.prepareRegistro();
            }
        });

        termosLnk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Register.this.toTermos();
            }
        });

        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Register.this.toLogin();
            }
        });
    }

    public void prepareRegistro()
    {
        String nome_v = nome.getText().toString();
        String email_v = email.getText().toString();
        String senha_v = senha.getText().toString();

        if(!StringHelper.notEmpty(nome_v)) {
            Register.this.errDialog("Informação necessária", "É necessário fornecer seu nome");
        }
        else if(!StringHelper.notEmpty(email_v)) {
            Register.this.errDialog("Informação necessária", "É necessário fornecer seu e-mail");
        }
        else if(!this.isValidEmail(email_v)) {
            Register.this.errDialog("Informação incorreta", "O e-mail deve ser válido");
        }
        else if(!StringHelper.notEmpty(senha_v)) {
            Register.this.errDialog("Informação necessária", "É necessário fornecer uma senha");
        }
        else if(!this.isValidSenha(senha_v)) {
            Register.this.errDialog("Informação incorreta", "A senha deve ter pelo menos 6 caracteres " +
                                                "e conter números, letras maiúscas e minúsculas e caracteres especiais como @,!,&");
        }
        else if(!termosCheck.isChecked()) {
            Register.this.errDialog("Ação necessária", "É necessário aceitar os termos de uso");
        }
        else {
            // faça o registro
            RegisterData register = new RegisterData(nome_v, email_v, senha_v);
            this.register(register);
        }
    }

    public void register(RegisterData register)
    {
        Register.this.showLoadingSpinner();
        UserAPI api = new UserAPI(Register.this);
        api.register(register, new ApiResponse<Object>() {
            @Override
            public void onResponse() {
                Register.this.hideLoadingSpinner();
                Register.this.successDialog("Sucesso", "Seu registro foi realizado com sucesso. Vamos fazer o login?", new DialogHelper() {
                    @Override
                    public void onCancel() {

                    }

                    @Override
                    public void onOk() {
                        Register.this.toLogin();
                    }
                });
            }

            @Override
            public void onResponse(Object data) {
                Register.this.hideLoadingSpinner();
            }

            @Override
            public void onResponse(Object data, int total) {
                Register.this.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() {
                Register.this.hideLoadingSpinner();
            }

            @Override
            public void onError(String msg) {
                Register.this.hideLoadingSpinner();
                Register.this.errDialog("Erro", msg);
                Log.d(TAG, "erro: "+msg);
            }

            @Override
            public void onFail(Object fail) {
                Register.this.hideLoadingSpinner();
                Register.this.errDialog("Erro", "Não consegui realizar a operação. Por favor, tente mais tarde");
                Log.d(TAG, "falha");
            }
        });
    }

    public void toLogin()
    {
        Intent intent = new Intent(
                Register.this,Login.class
        );
        startActivity(intent);
        finish();
    }

    public void toTermos()
    {
        Intent intent = new Intent(
                Register.this,Terms.class
        );
        startActivity(intent);
    }
}
