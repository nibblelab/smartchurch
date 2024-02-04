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
import com.nibblelab.smartchurch.model.LoginData;
import com.nibblelab.smartchurch.model.UserData;
import com.nibblelab.smartchurch.storage.UserStorage;

import java.io.IOException;

public class Login extends Base {

    public static final String TAG = "Login";

    Button login_btn;
    TextView esqueci;
    TextView register;
    EditText email;
    EditText senha;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        /* config */
        email = (EditText) findViewById(R.id.lg_email_fld);
        senha = (EditText) findViewById(R.id.lg_senha_fld);
        login_btn = (Button) findViewById(R.id.lg_acessar_btn);
        esqueci = (TextView) findViewById(R.id.lg_forget);
        register = (TextView) findViewById(R.id.lg_register);
        progress = (ProgressBar) findViewById(R.id.lg_progress);

        this.initLoadingSpinner();

        /* botão de login */
        login_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Login.this.prepareLogin();
            }
        });

        esqueci.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Login.this.toForget();
            }
        });

        register.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Login.this.toRegister();
            }
        });

        this.checkKeepedLogin();
    }

    public void prepareLogin()
    {
        String email_v = email.getText().toString();
        String senha_v = senha.getText().toString();

        if(email_v == null || email_v.equals("")) {
            Login.this.errDialog("Informação necessária", "É necessário fornecer o e-mail");
        }
        else if(senha_v == null || senha_v.equals("")) {
            Login.this.errDialog("Informação necessária", "É necessário fornecer a senha");
        }
        else {
            // faça o login
            LoginData login = new LoginData(email_v, senha_v);
            this.login(login);
        }
    }

    public void checkKeepedLogin()
    {
        // veja se o usuário já está logado
        if(this.isUserLogged())
        {
            this.setLastAcs();
            this.toMain();
        }
    }

    public void login(LoginData login)
    {
        Login.this.showLoadingSpinner();
        UserAPI api = new UserAPI(Login.this);
        api.login(login, new ApiResponse<UserData>() {
            @Override
            public void onResponse() {
                Login.this.hideLoadingSpinner();
            }

            @Override
            public void onResponse(UserData data) {
                Login.this.hideLoadingSpinner();
                Login.this.onLogin(data);
            }

            @Override
            public void onResponse(UserData data, int total) {
                Login.this.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                Login.this.hideLoadingSpinner();
                Login.this.errDialog("Erro", msg);
                Log.d(TAG, "erro: "+msg);
            }

            @Override
            public void onFail(Object fail) {
                Login.this.hideLoadingSpinner();
                Login.this.errDialog("Erro", "Não consegui realizar a operação. Por favor, tente mais tarde");
                Log.d(TAG, "falha");
            }

        });
    }

    public void onLogin(UserData user)
    {
        // armazene os dados do usuário logado
        UserStorage st = new UserStorage(this, getString(R.string.user_file));
        try {
            st.write(user);
            // vá para a tela principal
            this.toMain();
        } catch (IOException e) {
            Log.d(TAG, e.getMessage());
        }
    }

    public void toMain()
    {
        Intent intent = new Intent(
                Login.this,SmartChurch.class
        );
        startActivity(intent);
        finish();
    }

    public void toForget()
    {
        Intent intent = new Intent(
                Login.this,ForgetPass.class
        );
        startActivity(intent);
        finish();
    }

    public void toRegister()
    {
        Intent intent = new Intent(
                Login.this,Register.class
        );
        startActivity(intent);
        finish();
    }
}
