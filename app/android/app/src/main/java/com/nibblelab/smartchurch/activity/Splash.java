package com.nibblelab.smartchurch.activity;

import android.content.Intent;
import android.os.Handler;
import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;

import com.nibblelab.smartchurch.R;

public class Splash extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash);

        Handler handler = new Handler();
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                toLogin();
            }
        }, 3000);
    }

    private void toLogin() {
        Intent intent = new Intent(
                Splash.this,Login.class
        );
        startActivity(intent);
        finish();
    }
}
