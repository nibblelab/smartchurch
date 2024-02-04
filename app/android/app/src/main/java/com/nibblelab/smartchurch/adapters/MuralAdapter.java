package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import android.content.Intent;
import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.lifecycle.Lifecycle;
import androidx.recyclerview.widget.RecyclerView;

import com.ct7ct7ct7.androidvimeoplayer.view.VimeoPlayerView;
import com.nibblelab.smartchurch.API.BookmarkAPI;
import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.MediaHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.BookmarkData;
import com.nibblelab.smartchurch.model.MuralData;
import com.nibblelab.smartchurch.soundcloud.SoundCloudPlayerView;
import com.nibblelab.smartchurch.ui.events.MuralListEvents;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.listeners.AbstractYouTubePlayerListener;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.views.YouTubePlayerView;
import com.squareup.picasso.Picasso;

import java.io.UnsupportedEncodingException;
import java.util.List;

public class MuralAdapter extends RecyclerView.Adapter<MuralAdapter.RecyclerMuralViewHolder> {

    public static MuralListEvents events;
    Context ctx;
    Lifecycle lifecycle;
    private List<MuralData> list;
    Base b;

    public MuralAdapter(Context ctx, List<MuralData> list, MuralListEvents ev, Lifecycle lifecycle, Base b)
    {
        this.ctx = ctx;
        this.list = list;
        this.events = ev;
        this.lifecycle = lifecycle;
        this.b = b;
    }

    @NonNull
    @Override
    public MuralAdapter.RecyclerMuralViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int viewType) {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.mural_it_layout, viewGroup, false);
        return new MuralAdapter.RecyclerMuralViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull MuralAdapter.RecyclerMuralViewHolder viewHolder, int i) {

        MuralData c = list.get(i);
        String content4Share = c.getId();

        if(StringHelper.notEmpty(c.getImg()))
        {
            Picasso.get().load(ApiContants.RC_URL + "/" + c.getImg()).into(viewHolder.img);
            viewHolder.img.getLayoutParams().height = 1080;
            viewHolder.img.requestLayout();
            viewHolder.img.setVisibility(View.VISIBLE);
        }
        else if(StringHelper.notEmpty(c.getVideo()))
        {
            // tente obter como youtube
            String idVideo = MediaHelper.getYoutubeVideoId(c.getVideo());
            if(StringHelper.notEmpty(idVideo)) {
                viewHolder.youtubeView.setVisibility(View.VISIBLE);
                if(viewHolder.youtubePlayer != null) {
                    viewHolder.youtubePlayer.cueVideo(idVideo, 0);
                }
                else {
                    // player ainda não carregado. Marque para loading futuro
                    viewHolder.idVideo = idVideo;
                }
            }
            else {
                // tente obter como vimeo
                idVideo = MediaHelper.getVimeoVideoId(c.getVideo());
                if(StringHelper.notEmpty(idVideo)) {
                    viewHolder.vimeoView.setVisibility(View.VISIBLE);
                    viewHolder.vimeoView.initialize(false, Integer.parseInt(idVideo));
                }
            }
        }
        else if(StringHelper.notEmpty(c.getAudio()))
        {
            // tente obter como soundcloud
            String idAudio = MediaHelper.getSoundCloudAudioId(c.getAudio());
            if(StringHelper.notEmpty(idAudio)) {
                viewHolder.soundcoudView.setVisibility(View.VISIBLE);
                viewHolder.soundcoudView.loadAudio(idAudio);
            }
        }
        else if(StringHelper.notEmpty(c.getTitulo()))
        {
            viewHolder.titulo.setText(c.getTitulo());
            viewHolder.titulo.setVisibility(View.VISIBLE);

            if(StringHelper.notEmpty(c.getConteudo()))
            {
                viewHolder.texto.loadDataWithBaseURL(null, c.getConteudo(), "text/html", "utf-8", null);
            }
        }

        if(c.isMarked()) {
            viewHolder.likeBtn.setImageResource(R.drawable.ic_hearted);
        }
        else {
            viewHolder.likeBtn.setImageResource(R.drawable.ic_heart);
        }

        // botão gostei
        viewHolder.likeBtn.setOnClickListener(v -> {
            BookmarkAPI api = new BookmarkAPI(b);
            if(c.isMarked()) {
                api.remove(c.getMark_id(), new ApiResponse<Object>() {
                    @Override
                    public void onResponse() {
                        viewHolder.likeBtn.setImageResource(R.drawable.ic_heart);
                        events.onRemoveFromBookmark();
                    }

                    @Override
                    public void onResponse(Object data) {

                    }

                    @Override
                    public void onResponse(Object data, int total) {

                    }

                    @Override
                    public void onAlreadyExecuted() {

                    }

                    @Override
                    public void onError(String msg) {

                    }

                    @Override
                    public void onFail(Object fail) {

                    }
                });
            }
            else {
                BookmarkData d = new BookmarkData();
                d.setId(content4Share);
                d.setPessoa(b.getUser().getId());
                api.createForMural(d, new ApiResponse<Object>() {
                    @Override
                    public void onResponse() {
                        viewHolder.likeBtn.setImageResource(R.drawable.ic_hearted);
                    }

                    @Override
                    public void onResponse(Object data) {

                    }

                    @Override
                    public void onResponse(Object data, int total) {

                    }

                    @Override
                    public void onAlreadyExecuted() {

                    }

                    @Override
                    public void onError(String msg) {

                    }

                    @Override
                    public void onFail(Object fail) {

                    }
                });
            }

        });

        // botão share
        viewHolder.shareBtn.setOnClickListener(v -> {
            try {
                byte[] data = content4Share.getBytes("UTF-8");
                String muralLink = ApiContants.MURAL_URL + "/" + Base64.encodeToString(data, Base64.DEFAULT);

                Intent sendIntent = new Intent();
                sendIntent.setAction(Intent.ACTION_SEND);
                sendIntent.putExtra(Intent.EXTRA_TEXT, muralLink);
                sendIntent.setType("text/plain");

                Intent shareIntent = Intent.createChooser(sendIntent, null);
                this.ctx.startActivity(shareIntent);
            } catch (UnsupportedEncodingException e) {
                e.printStackTrace();
            }
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    protected class RecyclerMuralViewHolder extends RecyclerView.ViewHolder
    {
        protected ImageView img;
        protected TextView titulo;
        protected WebView texto;
        protected YouTubePlayerView youtubeView;
        protected YouTubePlayer youtubePlayer;
        protected VimeoPlayerView vimeoView;
        protected SoundCloudPlayerView soundcoudView;

        // caso do youtube (por conta do delay no loading da interface)
        protected String idVideo;

        // botões
        protected ImageView likeBtn;
        protected ImageView shareBtn;

        public RecyclerMuralViewHolder(final View v)
        {
            super(v);

            img = (ImageView) v.findViewById(R.id.mural_img);
            titulo = (TextView) v.findViewById(R.id.mural_titulo);
            texto = (WebView) v.findViewById(R.id.mural_texto);
            youtubeView = (YouTubePlayerView) v.findViewById(R.id.mural_youtube);
            vimeoView = (VimeoPlayerView) v.findViewById(R.id.mural_vimeo);
            soundcoudView = (SoundCloudPlayerView) v.findViewById(R.id.mural_soundcloud);

            likeBtn = (ImageView) v.findViewById(R.id.mural_like);
            shareBtn = (ImageView) v.findViewById(R.id.mural_share);

            lifecycle.addObserver(youtubeView);
            youtubeView.addYouTubePlayerListener(new AbstractYouTubePlayerListener() {
                @Override
                public void onReady(@NonNull YouTubePlayer youTubePlayer) {
                    youtubePlayer = youTubePlayer;
                    if(StringHelper.notEmpty(idVideo)) {
                        youtubePlayer.cueVideo(idVideo, 0);
                    }
                }
            });

            lifecycle.addObserver(vimeoView);
            lifecycle.addObserver(soundcoudView);

            img.setVisibility(View.GONE);
            titulo.setVisibility(View.GONE);
            texto.setVisibility(View.GONE);
            youtubeView.setVisibility(View.GONE);
            vimeoView.setVisibility(View.GONE);
            soundcoudView.setVisibility(View.GONE);

        }
    }
}
