package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.annotation.NonNull;
import androidx.lifecycle.Lifecycle;
import androidx.recyclerview.widget.RecyclerView;

import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.TransmissaoData;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.listeners.AbstractYouTubePlayerListener;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.views.YouTubePlayerView;

import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class TransmissaoAdapter extends RecyclerView.Adapter<TransmissaoAdapter.RecyclerTransmissaoViewHolder> {

    Context ctx;
    private List<TransmissaoData> list;
    Lifecycle lifecycle;

    public TransmissaoAdapter(Context ctx, List<TransmissaoData> list, Lifecycle lifecycle)
    {
        this.ctx = ctx;
        this.list = list;
        this.lifecycle = lifecycle;
    }

    @NonNull
    @Override
    public RecyclerTransmissaoViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i)
    {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.transmissao_it_layout, viewGroup, false);
        return new RecyclerTransmissaoViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerTransmissaoViewHolder viewHolder, int i) {
        TransmissaoData c = list.get(i);
        String idVideo = "";

        if(c.getVideo().contains("youtube")) {
            // youtube
            Pattern pattern = Pattern.compile("youtube\\.com/watch\\?v=([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(c.getVideo());
            while (matcher.find()) {
                idVideo = matcher.group(1);
            }
        }
        else if(c.getVideo().contains("youtu.be")) {
            // youtube reduzido
            Pattern pattern = Pattern.compile("youtu\\.be/([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(c.getVideo());
            while (matcher.find()) {
                idVideo = matcher.group(1);
            }
        }

        if(StringHelper.notEmpty(idVideo)) {
            viewHolder.idVideo = idVideo;
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    protected class RecyclerTransmissaoViewHolder extends RecyclerView.ViewHolder
    {
        protected YouTubePlayerView playerView;
        protected YouTubePlayer player;
        protected String idVideo;

        public RecyclerTransmissaoViewHolder(final View v)
        {
            super(v);

            playerView = (YouTubePlayerView) v.findViewById(R.id.transmissao_youtube);

            lifecycle.addObserver(playerView);
            playerView.addYouTubePlayerListener(new AbstractYouTubePlayerListener() {
                @Override
                public void onReady(@NonNull YouTubePlayer youTubePlayer) {
                    player = youTubePlayer;
                    player.cueVideo(idVideo, 0);
                }
            });
        }
    }
}
