package com.nibblelab.smartchurch.activity;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.nibblelab.smartchurch.API.UserAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DialogHelper;
import com.nibblelab.smartchurch.common.StringHelper;

public class ForgetPass extends Base {

    public static final String TAG = "Forget";

    Button forget_btn;
    EditText email;
    TextView back;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgetpass);

        /* config */
        email = (EditText) findViewById(R.id.fgt_email_fld);
        forget_btn = (Button) findViewById(R.id.forget_btn);
        back = (TextView) findViewById(R.id.fgt_back);
        progress = (ProgressBar) findViewById(R.id.fgt_progress);

        this.initLoadingSpinner();

        /* botão de esqueci */
        forget_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ForgetPass.this.prepareEsquerci();
            }
        });

        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ForgetPass.this.toLogin();
            }
        });
    }

    public void prepareEsquerci()
    {
        String email_v = email.getText().toString();

        if(!StringHelper.notEmpty(email_v)) {
            ForgetPass.this.errDialog("Informação necessária", "É necessário fornecer seu e-mail");
        }
        else if(!this.isValidEmail(email_v)) {
            ForgetPass.this.errDialog("Informação incorreta", "O e-mail deve ser válido");
        }
        else {
            this.resetPwd(email_v);
        }
    }

    public void resetPwd(String email)
    {
        ForgetPass.this.showLoadingSpinner();
        UserAPI api = new UserAPI(ForgetPass.this);
        api.resetPwd(email, new ApiResponse<Object>() {
            @Override
            public void onResponse() {
                ForgetPass.this.hideLoadingSpinner();
                ForgetPass.this.successDialog("Sucesso", "Seu pedido de mundança de senha foi concluído com sucesso. "+
                        "As instruções para continuar o processo foram enviadas para seu e-mail.", new DialogHelper() {
                    @Override
                    public void onCancel() {

                    }

                    @Override
                    public void onOk() {
                        ForgetPass.this.toLogin();
                    }
                });
            }

            @Override
            public void onResponse(Object data) {
                ForgetPass.this.hideLoadingSpinner();
            }

            @Override
            public void onResponse(Object data, int total) {
                ForgetPass.this.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() {
                ForgetPass.this.hideLoadingSpinner();
            }

            @Override
            public void onError(String msg) {
                ForgetPass.this.hideLoadingSpinner();
                ForgetPass.this.errDialog("Erro", msg);
                Log.d(TAG, "erro: "+msg);
            }

            @Override
            public void onFail(Object fail) {
                ForgetPass.this.hideLoadingSpinner();
                ForgetPass.this.errDialog("Erro", "Não consegui realizar a operação. Por favor, tente mais tarde");
                Log.d(TAG, "falha");
            }
        });
    }

    public void toLogin()
    {
        Intent intent = new Intent(
                ForgetPass.this,Login.class
        );
        startActivity(intent);
        finish();
    }
}
