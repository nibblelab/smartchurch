package com.nibblelab.smartchurch.activity;

import android.content.DialogInterface;
import android.os.Bundle;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DialogHelper;
import com.nibblelab.smartchurch.common.InternetCheckHelper;
import com.nibblelab.smartchurch.model.SmartChurchData;
import com.nibblelab.smartchurch.model.TokenData;
import com.nibblelab.smartchurch.model.UserData;
import com.nibblelab.smartchurch.storage.SmartChurchStorage;
import com.nibblelab.smartchurch.storage.TokenStorage;
import com.nibblelab.smartchurch.storage.UserStorage;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.Date;
import java.util.Timer;
import java.util.TimerTask;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Base extends AppCompatActivity {

    protected Toolbar toolbar;
    protected ProgressBar progress;
    protected TokenData token;
    protected UserData user;
    protected SmartChurchData appData;

    public static final String TAG = "Base";

    Timer timer;
    public final long timerDelayShort = 60000; // 1 minuto
    public final long timerDelayLong = 30 * timerDelayShort; // 30 minutos
    public long timerDelay = timerDelayShort;
    public int offlineCounter = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        this.getUserToken();
        this.getUserData();
        this.getSmartChurchData();

        // verifique a conectividade
        this.checkInternet();
        timer = new Timer();
        this.setInternetAutoTest();
    }

    public void setToolbarTitle(String title)
    {
        toolbar.setTitle(title);
    }

    public void setToolbarTitle(int resourceId)
    {
        setToolbarTitle(getResources().getString(resourceId));
    }

    public void setUserToken(String t)
    {
        TokenData token = new TokenData();
        token.setToken(t);
        TokenStorage st = new TokenStorage(this, getString(R.string.token_file));
        try {
            st.write(token);
        } catch (IOException e) {
            Log.d(TAG, e.getMessage());
        }
    }

    public void getUserToken()
    {
        TokenStorage st = new TokenStorage(this, getString(R.string.token_file));
        try {
            token = st.read();
        } catch (FileNotFoundException e) {
            Log.d(TAG, e.getMessage());
        } catch (IOException e) {
            Log.d(TAG, e.getMessage());
        }
    }

    public String getUserTokenValue()
    {
        this.getUserToken();
        return token.getToken();
    }

    public boolean isUserLogged()
    {
        return (this.token != null && !this.token.getToken().equals(""));
    }

    public void getSmartChurchData()
    {
        SmartChurchStorage st = new SmartChurchStorage(this, getString(R.string.smartchurch_file));
        try {
            appData = st.read();
        } catch (FileNotFoundException e) {
            appData = new SmartChurchData();
            Log.d(TAG, e.getMessage());
        } catch (IOException e) {
            appData = new SmartChurchData();
            Log.d(TAG, e.getMessage());
        }
    }

    public SmartChurchData getAppData()
    {
        return this.appData;
    }

    public void setLastAcs()
    {
        appData.setLastAcs(new Date());
        SmartChurchStorage st = new SmartChurchStorage(this, getString(R.string.smartchurch_file));
        try {
            st.write(appData);
        } catch (IOException e) {
            Log.d(TAG, e.getMessage());
        }
    }

    public void getUserData()
    {
        UserStorage st = new UserStorage(this, getString(R.string.user_file));
        try {
            user = st.read();
        } catch (FileNotFoundException e) {
            Log.d(TAG, e.getMessage());
        } catch (IOException e) {
            Log.d(TAG, e.getMessage());
        }
    }

    public UserData getUser()
    {
        return this.user;
    }

    public boolean userHasPermission(String perm)
    {
        if(user == null) {
            this.getUserData();
        }

        return UserData.doIHavePermission(user.getPerms(), perm);
    }

    public boolean userHasModulo(String modulo)
    {
        if(user == null) {
            this.getUserData();
        }

        return UserData.doIHaveMod(user.getModulos(), modulo);
    }

    public boolean userHasIgrejaData()
    {
        if(user == null) {
            this.getUserData();
        }

        return this.user.getMembresia().hasIgreja();
    }

    public void errDialog(String title, String msg, final DialogHelper r)
    {
        AlertDialog alertDialog = new AlertDialog.Builder(this, R.style.SmartChurchAppTheme_DialogDanger).create();
        alertDialog.setTitle(title);
        alertDialog.setMessage(msg);
        alertDialog.setButton(AlertDialog.BUTTON_POSITIVE, "Ok",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        r.onOk();
                    }
                });
        alertDialog.show();
    }

    public void errDialog(String title, String msg)
    {
        this.errDialog(title, msg, new DialogHelper() {
            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {

            }
        });
    }

    public void warningDialog(String title, String msg, final DialogHelper r)
    {
        AlertDialog alertDialog = new AlertDialog.Builder(this, R.style.SmartChurchAppTheme_DialogWarning).create();
        alertDialog.setTitle(title);
        alertDialog.setMessage(msg);
        alertDialog.setButton(AlertDialog.BUTTON_POSITIVE, "Ok",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        r.onOk();
                    }
                });
        alertDialog.show();
    }

    public void warningDialog(String title, String msg)
    {
        this.warningDialog(title, msg, new DialogHelper() {
            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {

            }
        });
    }

    public void infoDialog(String title, String msg, final DialogHelper r)
    {
        AlertDialog alertDialog = new AlertDialog.Builder(this, R.style.SmartChurchAppTheme_DialogInfo).create();
        alertDialog.setTitle(title);
        alertDialog.setMessage(msg);
        alertDialog.setButton(AlertDialog.BUTTON_POSITIVE, "Ok",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        r.onOk();
                    }
                });
        alertDialog.show();
    }

    public void infoDialog(String title, String msg)
    {
        this.infoDialog(title, msg, new DialogHelper() {
            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {

            }
        });
    }

    public void successDialog(String title, String msg, final DialogHelper r)
    {
        AlertDialog alertDialog = new AlertDialog.Builder(this, R.style.SmartChurchAppTheme_DialogSuccess).create();
        alertDialog.setTitle(title);
        alertDialog.setMessage(msg);
        alertDialog.setButton(AlertDialog.BUTTON_POSITIVE, "Ok",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        r.onOk();
                    }
                });
        alertDialog.show();
    }

    public void successDialog(String title, String msg)
    {
        this.successDialog(title, msg, new DialogHelper() {
            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {

            }
        });
    }

    public void confirmDialog(String title, String msg, final DialogHelper r)
    {
        AlertDialog alertDialog = new AlertDialog.Builder(this, R.style.SmartChurchAppTheme_DialogWarning).create();
        alertDialog.setTitle(title);
        alertDialog.setMessage(msg);
        alertDialog.setCancelable(true);
        alertDialog.setButton(AlertDialog.BUTTON_POSITIVE, "Ok",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        r.onOk();
                    }
                });
        alertDialog.setButton(AlertDialog.BUTTON_NEGATIVE, "Cancelar", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
                r.onCancel();
            }
        });
        alertDialog.show();
    }

    public void confirmDialog(String title, String msg)
    {
        this.confirmDialog(title, msg, new DialogHelper() {
            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {

            }
        });
    }

    public void initLoadingSpinner()
    {
        if(progress == null)
            return;
        progress.setVisibility(View.GONE);
    }

    public void showLoadingSpinner()
    {
        progress.setVisibility(View.VISIBLE);
    }

    public void hideLoadingSpinner()
    {
        progress.setVisibility(View.GONE);
    }

    public void doLogout() {}

    /**
     * Valida se uma string tem o padrão de email
     *
     * @param email
     * @return
     */
    public boolean isValidEmail(String email)
    {
        Pattern pattern = Pattern.compile("^[\\w-_\\.+]*[\\w-_\\.]\\@([\\w]+\\.)+[\\w]+[\\w]$", Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(email);
        return matcher.matches();
    }

    /**
     * Valida uma senha
     *
     * @param senha
     * @return
     */
    public boolean isValidSenha(String senha)
    {
        Pattern pattern = Pattern.compile("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[$@$!%*?&])[A-Za-z\\d$@$!%*?&]{6,}", Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(senha);
        return matcher.matches();
    }

    /**
     * Configura os teste automáticos de internet
     */
    public void setInternetAutoTest()
    {
        timer.schedule(new TimerTask() {
            @Override
            public void run() {
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Base.this.checkInternet();
                    }
                });
            }
        }, timerDelay, timerDelay);
    }

    /**
     * Pare os testes automáticos de internet
     */
    public void stopInternetAutoTest()
    {
        timer.cancel();
        timer.purge();
    }

    /**
     * Mostre a mensagem de aviso de falta de conexão com internet
     */
    public void showNoInternetWarning()
    {
        Toast.makeText(this, R.string.no_internet, Toast.LENGTH_LONG).show();
    }

    /**
     * Verifica a conexão com a internet
     */
    public void checkInternet()
    {
        new InternetCheckHelper(internet -> {
            if(!internet) {
                showNoInternetWarning();
                Log.d(TAG, "sem internet");
                offlineCounter++; // aumente o contador de offline
            }
            else {
                /**
                 * reduza o contador de offline. Se ele zerar ou ficar menor que zero
                 * aumente o tempo de checagem pra evitar gasto desnecessário de rede
                 * em situações que a internet está online
                 */
                offlineCounter--;
                if(offlineCounter <= 0) {
                    timerDelay = timerDelayLong;
                }
            }
        });
    }
}
