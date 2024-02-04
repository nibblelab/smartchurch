package com.nibblelab.smartchurch.activity;

import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.webkit.WebView;
import android.widget.ProgressBar;

import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.nibblelab.smartchurch.API.TermosAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;


public class Terms extends Base {

    public static final String TAG = "Terms";

    WebView termos;
    FloatingActionButton back;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_terms);

        termos = (WebView) findViewById(R.id.termos_txt);
        progress = (ProgressBar) findViewById(R.id.terms_progress);
        back = (FloatingActionButton) findViewById(R.id.term_back);


        termos.setBackgroundColor(Color.TRANSPARENT);

        getTermos();

        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Terms.this.toRegister();
            }
        });

    }

    public void getTermos()
    {
        this.showLoadingSpinner();
        TermosAPI api = new TermosAPI(this);
        api.termos(new ApiResponse<String>() {
            @Override
            public void onResponse() {
                hideLoadingSpinner();
            }

            @Override
            public void onResponse(String data) {
                hideLoadingSpinner();
                // workaroud por conta de bug no padding da webview android
                String html = "<div style=\"padding-bottom: 90px;\">" + data + "</div>";
                termos.loadDataWithBaseURL(null, html, "text/html", "utf-8", null);
            }

            @Override
            public void onResponse(String data, int total) {
                hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                hideLoadingSpinner();
                Log.d(TAG, "Erro: " + msg);
            }

            @Override
            public void onFail(Object fail) {
                hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    public void toRegister()
    {
        finish();
    }
}