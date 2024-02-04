package com.nibblelab.smartchurch.soundcloud;

import android.os.Handler;
import android.os.Looper;
import android.webkit.JavascriptInterface;

import java.util.ArrayList;

public class SoundCloudJS {

    final Handler mainThreadHandler;
    ArrayList<SoundCloudPlayerListenner> listenners;

    public SoundCloudJS() {
        listenners = new ArrayList<>();
        this.mainThreadHandler = new Handler(Looper.getMainLooper());
    }

    public void addListenner(SoundCloudPlayerListenner l) {
        this.listenners.add(l);
    }

    @JavascriptInterface
    public void sendReady() {
        for(final SoundCloudPlayerListenner l : listenners) {
            mainThreadHandler.post(new Runnable() {
                @Override
                public void run() {
                    l.onReady();
                }
            });
        }
    }
}
