package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.lifecycle.Lifecycle;
import androidx.recyclerview.widget.RecyclerView;

import com.ct7ct7ct7.androidvimeoplayer.view.VimeoPlayerView;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DateHelper;
import com.nibblelab.smartchurch.common.MediaHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.AgendaData;
import com.nibblelab.smartchurch.model.AgendaResponsavelData;
import com.nibblelab.smartchurch.model.TagAgendaData;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.listeners.AbstractYouTubePlayerListener;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.views.YouTubePlayerView;

import java.util.ArrayList;
import java.util.List;

public class AgendaAdapter extends RecyclerView.Adapter<AgendaAdapter.RecyclerAgendaViewHolder> {

    Context ctx;
    private String data;
    private List<AgendaData> list;
    private List<TagAgendaData> tags;
    private Lifecycle lifecycle;
    private List<AgendaAdapter.RecyclerAgendaViewHolder> playerViews;
    private List<AgendaResponsavelData> responsaveis;

    public AgendaAdapter(Context ctx, String data, List<AgendaData> list, List<TagAgendaData> tags, Lifecycle lifecycle, List<AgendaResponsavelData> responsaveis)
    {
        this.ctx = ctx;
        this.data = data;
        this.list = list;
        this.tags = tags;
        this.lifecycle = lifecycle;
        this.playerViews = new ArrayList<>();
        this.responsaveis = responsaveis;
    }

    @NonNull
    @Override
    public AgendaAdapter.RecyclerAgendaViewHolder onCreateViewHolder(@NonNull ViewGroup viewGroup, int i)
    {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.evento_it_layout, viewGroup, false);
        return new AgendaAdapter.RecyclerAgendaViewHolder(itemView);
    }

    public RelativeLayout generateTagLayout(TagAgendaData tag)
    {
        // gera o layout e seta texto e cor
        RelativeLayout tag_layout = (RelativeLayout) LayoutInflater.from(this.ctx).inflate(R.layout.tag_data_layout, null);
        tag_layout.setBackgroundColor(tag.getColor());
        TextView tagView = tag_layout.findViewById(R.id.tag_nome);
        tagView.setText(tag.getTag());
        tagView.setTextColor(Color.WHITE);

        // aplica as margens
        LinearLayout.LayoutParams tagLayoutParams = new LinearLayout.LayoutParams(
                new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.WRAP_CONTENT,
                        LinearLayout.LayoutParams.WRAP_CONTENT));
        tagLayoutParams.setMargins(0, 0, 20, 0);
        tag_layout.setLayoutParams(tagLayoutParams);
        tag_layout.requestLayout();

        RelativeLayout.LayoutParams tagTextParams = new RelativeLayout.LayoutParams(
                new RelativeLayout.LayoutParams(
                        RelativeLayout.LayoutParams.WRAP_CONTENT,
                        RelativeLayout.LayoutParams.WRAP_CONTENT));
        tagTextParams.setMargins(20, 5, 20, 5);
        tagView.setLayoutParams(tagTextParams);

        return tag_layout;
    }

    public String generateHorario(AgendaData d)
    {
        if(d.isRecorrente())
        {
            return d.getTimeFromRecorrenteInDate(this.data);
        }

        String horario = "";

        if(DateHelper.equalDatesFromString(this.data, DateHelper.getHumanDateFromDBDateTime(d.getTime_ini())) &&
                DateHelper.equalDatesFromString(this.data, DateHelper.getHumanDateFromDBDateTime(d.getTime_end())))
        {
            // evento de um dia só
            horario = DateHelper.fromDBTimeToHumanTime(d.getTime_ini(), true, false) + " - " +
                    DateHelper.fromDBTimeToHumanTime(d.getTime_end(), true, false);
        }
        else {
            // evento de mais de um dia
            if(DateHelper.equalDatesFromString(this.data, DateHelper.getHumanDateFromDBDateTime(d.getTime_ini())))
            {
                // data do começo do evento. Começa na hora de início e vai até o fim do dia
                horario = DateHelper.fromDBTimeToHumanTime(d.getTime_ini(), true, false) + " - 23:59";
            }
            else if(DateHelper.equalDatesFromString(this.data, DateHelper.getHumanDateFromDBDateTime(d.getTime_end())))
            {
                // data do fim do evento. Começa no início do dia e vai até a hora de término
                horario = "00:51 - " + DateHelper.fromDBTimeToHumanTime(d.getTime_end(), true, false);
            }
            else {
                // data entre o início e fim do evento
                horario = "dia inteiro";
            }
        }

        return horario;
    }

    public void addtoPlayerViews(AgendaAdapter.RecyclerAgendaViewHolder viewHolder)
    {
        AgendaAdapter.RecyclerAgendaViewHolder finded = this.playerViews.stream()
                .filter( t -> t.getItemId() == viewHolder.getItemId())
                .findAny()
                .orElse(null);

        if(finded == null)
        {
            this.playerViews.add(viewHolder);
        }
    }

    @Override
    public void onBindViewHolder(@NonNull AgendaAdapter.RecyclerAgendaViewHolder viewHolder, int i) {
        AgendaData c = list.get(i);
        viewHolder.eventoTitulo.setText(c.getNome());

        // responsável
        viewHolder.eventoResponsavel.setVisibility(View.GONE);
        if(StringHelper.notEmpty(c.getResponsavel())) {
            AgendaResponsavelData responsavel = responsaveis
                                                    .stream()
                                                    .filter( t -> t.getId().equals(c.getResponsavel()))
                                                    .findAny()
                                                    .orElse(null);
            if(responsavel != null) {
                viewHolder.eventoResponsavel.setText((responsavel.getNome()));
                viewHolder.eventoResponsavel.setVisibility(View.VISIBLE);
            }
        }

        // tags
        if(c.getTags().size() > 0)
        {
            for(String tag_id : c.getTags())
            {
                TagAgendaData tag = tags
                                    .stream()
                                    .filter( t -> t.getId().equals(tag_id))
                                    .findAny()
                                    .orElse(null);
                if(tag != null)
                {
                    viewHolder.tags.addView(this.generateTagLayout(tag));
                }
            }
        }

        // horário
        viewHolder.eventoHorario.setText(this.generateHorario(c));

        // observações
        if(StringHelper.notEmpty(c.getObservacoes()))
        {
            String obs = "<p style='text-align: justify'>" + c.getObservacoes() + "</p>";
            viewHolder.eventoObservacoes.loadDataWithBaseURL(null, obs, "text/html", "utf-8", null);
            viewHolder.eventoObservacoes.setVisibility(View.VISIBLE);
        }
        else {
            viewHolder.eventoObservacoes.setVisibility(View.GONE);
        }

        // youtube
        if(StringHelper.notEmpty(c.getYoutube()))
        {
            String idVideo = MediaHelper.getYoutubeVideoId(c.getYoutube());
            if(StringHelper.notEmpty(idVideo))
            {
                this.lifecycle.addObserver(viewHolder.eventoYoutubeView);
                viewHolder.eventoYoutubeView.addYouTubePlayerListener(new AbstractYouTubePlayerListener() {
                    @Override
                    public void onReady(@NonNull YouTubePlayer youTubePlayer) {
                        viewHolder.eventoYoutubePlayer = youTubePlayer;
                        viewHolder.eventoYoutubePlayer.cueVideo(idVideo, 0);
                    }
                });
                viewHolder.eventoYoutubeView.setVisibility(View.VISIBLE);

                addtoPlayerViews(viewHolder);
            }
        }
        else {
            viewHolder.eventoYoutubeView.setVisibility(View.GONE);
        }

        // vimeo
        if(StringHelper.notEmpty(c.getVimeo()))
        {
            String idVimeo = MediaHelper.getVimeoVideoId(c.getVimeo());
            if(StringHelper.notEmpty(idVimeo))
            {
                this.lifecycle.addObserver(viewHolder.eventoVimeoPlayer);
                viewHolder.eventoVimeoPlayer.initialize(false, Integer.parseInt(idVimeo));
                viewHolder.eventoVimeoPlayer.setVisibility(View.VISIBLE);

                addtoPlayerViews(viewHolder);
            }
        }
        else {
            viewHolder.eventoVimeoPlayer.setVisibility(View.GONE);
        }

        // endereço
        if(c.hasEnderecoData()) {
            String endereco = c.getEndereco();
            if(StringHelper.notEmpty(c.getNumero())) {
                endereco += ", " + c.getNumero();
            }
            if(StringHelper.notEmpty(c.getComplemento())) {
                endereco += " - " + c.getComplemento();
            }
            viewHolder.eventoRua.setText(endereco);
            viewHolder.eventoBairro.setText(c.getBairro());

            String cidade = c.getCidade();
            if(StringHelper.notEmpty(c.getUf())) {
                cidade += "/" + c.getUf();
            }
            viewHolder.eventoCidade.setText(cidade);
            viewHolder.eventoCep.setText(c.getCep());

            viewHolder.eventoMap.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW,
                        Uri.parse("https://www.google.com/maps/search/?api=1&query="+c.getEnderecoUrl()));
                AgendaAdapter.this.ctx.startActivity(intent);
            });

            viewHolder.eventoEndereco.setVisibility(View.VISIBLE);
        }
        else {
            viewHolder.eventoEndereco.setVisibility(View.GONE);
        }

        // site
        if(StringHelper.notEmpty(c.getSite())) {
            viewHolder.eventoSite.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW,
                        Uri.parse(c.getSite()));
                AgendaAdapter.this.ctx.startActivity(intent);
            });
            viewHolder.eventoSite.setVisibility(View.VISIBLE);
        }
        else {
            viewHolder.eventoSite.setVisibility(View.INVISIBLE);
        }

        // facebook
        if(StringHelper.notEmpty(c.getFacebook())) {
            viewHolder.eventoFacebook.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW,
                        Uri.parse(c.getFacebook()));
                AgendaAdapter.this.ctx.startActivity(intent);
            });
            viewHolder.eventoFacebook.setVisibility(View.VISIBLE);
        }
        else {
            viewHolder.eventoFacebook.setVisibility(View.INVISIBLE);
        }

        // instagram
        if(StringHelper.notEmpty(c.getInstagram())) {
            viewHolder.eventoInstagram.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW,
                        Uri.parse(c.getInstagramUrl()));
                AgendaAdapter.this.ctx.startActivity(intent);
            });
            viewHolder.eventoInstagram.setVisibility(View.VISIBLE);
        }
        else {
            viewHolder.eventoInstagram.setVisibility(View.INVISIBLE);
        }

    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public void stopPlayers()
    {
        for(AgendaAdapter.RecyclerAgendaViewHolder vh : this.playerViews)
        {
            if(vh.eventoYoutubePlayer != null) {
                vh.eventoYoutubePlayer.pause();
            }
            if(vh.eventoVimeoPlayer != null) {
                vh.eventoVimeoPlayer.pause();
            }
        }
    }
    
    protected class RecyclerAgendaViewHolder extends RecyclerView.ViewHolder
    {
        protected TextView eventoTitulo;
        protected TextView eventoResponsavel;
        protected LinearLayout tags;
        protected TextView eventoHorario;
        protected WebView eventoObservacoes;
        protected YouTubePlayerView eventoYoutubeView;
        protected YouTubePlayer eventoYoutubePlayer;
        protected VimeoPlayerView eventoVimeoPlayer;
        protected LinearLayout eventoEndereco;
        protected TextView eventoRua;
        protected TextView eventoBairro;
        protected TextView eventoCidade;
        protected TextView eventoCep;
        protected ImageView eventoMap;
        protected ImageView eventoSite;
        protected ImageView eventoFacebook;
        protected ImageView eventoInstagram;

        public RecyclerAgendaViewHolder(final View v)
        {
            super(v);

            eventoTitulo = (TextView) v.findViewById(R.id.evento_titulo);
            eventoResponsavel = (TextView) v.findViewById(R.id.evento_responsavel);

            tags = (LinearLayout) v.findViewById(R.id.evento_tags);
            eventoHorario = (TextView) v.findViewById(R.id.evento_horario);

            eventoObservacoes = (WebView) v.findViewById(R.id.evento_observacoes);
            eventoYoutubeView = (YouTubePlayerView) v.findViewById(R.id.evento_youtube);
            eventoVimeoPlayer = (VimeoPlayerView) v.findViewById(R.id.evento_vimeo);

            eventoObservacoes.setVisibility(View.INVISIBLE);
            eventoObservacoes.setBackgroundColor(Color.TRANSPARENT);

            eventoYoutubeView.setVisibility(View.GONE);
            eventoVimeoPlayer.setVisibility(View.GONE);

            eventoEndereco = (LinearLayout) v.findViewById(R.id.evento_endereco);
            eventoRua = (TextView) v.findViewById(R.id.evento_rua);
            eventoBairro = (TextView) v.findViewById(R.id.evento_bairro);
            eventoCidade = (TextView) v.findViewById(R.id.evento_cidade);
            eventoCep = (TextView) v.findViewById(R.id.evento_cep);
            eventoMap = (ImageView) v.findViewById(R.id.evento_map);

            eventoSite = (ImageView) v.findViewById(R.id.evento_site);
            eventoFacebook = (ImageView) v.findViewById(R.id.evento_facebook);
            eventoInstagram = (ImageView) v.findViewById(R.id.evento_instagram);

        }
    }
}
