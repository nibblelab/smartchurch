package com.nibblelab.smartchurch.soundcloud;

import android.content.Context;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.os.Build;
import android.util.AttributeSet;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.ProgressBar;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.lifecycle.Lifecycle;
import androidx.lifecycle.LifecycleObserver;
import androidx.lifecycle.OnLifecycleEvent;

public class SoundCloudPlayerView extends FrameLayout implements LifecycleObserver {

    SoundCloudPlayer player;
    SoundCloudJS js;
    ProgressBar progressBar;
    boolean hasAudio;

    public SoundCloudPlayerView(@NonNull Context context) {
        this(context, null);
    }

    public SoundCloudPlayerView(@NonNull Context context, AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public SoundCloudPlayerView(@NonNull Context context, @Nullable AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);

        this.player = new SoundCloudPlayer(context);
        FrameLayout.LayoutParams playerLp = new FrameLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.MATCH_PARENT);
        playerLp.gravity = Gravity.CENTER;
        this.addView(player, playerLp);

        this.progressBar = new ProgressBar(context);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            progressBar.setIndeterminate(true);
            progressBar.setIndeterminateTintMode(PorterDuff.Mode.SRC_ATOP);
            progressBar.setIndeterminateTintList(ColorStateList.valueOf(Color.rgb(0, 172, 240)));
        }
        LayoutParams progressLayoutParams = new FrameLayout.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
        progressLayoutParams.gravity = Gravity.CENTER;
        this.addView(progressBar, progressLayoutParams);

        js = new SoundCloudJS();
        hasAudio = false;
    }

    public void loadAudio(String id) {

        js.addListenner(new SoundCloudPlayerListenner() {
            @Override
            public void onReady() {
                progressBar.setVisibility(View.GONE);
            }
        });

        progressBar.setVisibility(View.VISIBLE);
        this.player.load(id, js);
        hasAudio = true;
    }

    @OnLifecycleEvent(Lifecycle.Event.ON_PAUSE)
    public void pauseAudio() {
        if(hasAudio) {
            this.player.pause();
        }
    }



}
