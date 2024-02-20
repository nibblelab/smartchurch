package com.nibblelab.smartchurch.soundcloud;

import android.content.Context;
import android.graphics.Bitmap;
import android.util.AttributeSet;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import com.nibblelab.smartchurch.R;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

public class SoundCloudPlayer extends WebView {

    SoundCloudJS js;

    public SoundCloudPlayer(Context context) {
        super(context);
    }

    public SoundCloudPlayer(Context context, AttributeSet attrs) {
        super(context, attrs);
    }

    public SoundCloudPlayer(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
    }

    public void pause()
    {
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.KITKAT) {
            evaluateJavascript("javascript:pauseAudio()", null);
        }
        else {
            loadUrl("javascript:pauseAudio();");
        }
    }

    public void load(final String id, SoundCloudJS js) {
        this.js = js;
        String cnt = this.readSoundCloudPlayerHTMLFromFile();

        this.getSettings().setJavaScriptEnabled(true);
        this.getSettings().setSupportMultipleWindows(false);
        this.getSettings().setMediaPlaybackRequiresUserGesture(false);
        this.getSettings().setCacheMode(WebSettings.LOAD_NO_CACHE);
        this.getSettings().setDatabaseEnabled(false);
        this.getSettings().setDomStorageEnabled(false);
        this.getSettings().setAllowFileAccess(false);

        // user agent pra tirar o aviso nojento que soundcloud coloca em webview
        String DESKTOP_USER_AGENT = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36";
        this.getSettings().setUserAgentString(DESKTOP_USER_AGENT);

        // captura os eventos js e transmite pro java
        this.addJavascriptInterface(this.js, "SoundCloudJS");

        String soundcloud_src = "https://w.soundcloud.com/player/?url="+
                "https%3A//api.soundcloud.com/tracks/"+ id +
                "&color=%23ff5500&auto_play=false&hide_related=false"+
                "&show_comments=false&show_user=false&show_reposts=false"+
                "&show_teaser=true&visual=false&show_artwork=false"+
                "&sharing=false&download=false&buying=false";

        final String formattedString = cnt.replace("<SOUNDCLOUD_SRC>", soundcloud_src);

        this.loadDataWithBaseURL(null, formattedString, "text/html", "utf-8", null);

        this.setWebChromeClient(new WebChromeClient() {
            @Override
            public Bitmap getDefaultVideoPoster() {
                Bitmap result = super.getDefaultVideoPoster();

                if (result == null)
                    return Bitmap.createBitmap(1, 1, Bitmap.Config.RGB_565);
                else
                    return result;
            }
        });

        WebViewClient webViewClient = new WebViewClient() {
            @Override
            public void onPageFinished(WebView webView, String url) {

            }
        };
        setWebViewClient(webViewClient);
    }

    private String readSoundCloudPlayerHTMLFromFile() {
        try {
            InputStream inputStream = getResources().openRawResource(R.raw.soundcloud);

            InputStreamReader inputStreamReader = new InputStreamReader(inputStream, "utf-8");
            BufferedReader bufferedReader = new BufferedReader(inputStreamReader);

            String read;
            StringBuilder sb = new StringBuilder();

            while ((read = bufferedReader.readLine()) != null)
                sb.append(read).append("\n");
            inputStream.close();

            return sb.toString();
        } catch (Exception e) {
            throw new RuntimeException("Can't parse HTML file containing the player.");
        }
    }

    public void recycle() {
        this.setTag(null);
    }

}
