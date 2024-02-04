package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;


import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.AppCompatSpinner;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.ct7ct7ct7.androidvimeoplayer.view.VimeoPlayerView;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.nibblelab.smartchurch.API.SerieEstudoAPI;
import com.nibblelab.smartchurch.API.EstudoAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.EstudoAdapter;
import com.nibblelab.smartchurch.common.MediaHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.EstudoData;
import com.nibblelab.smartchurch.model.SerieEstudoData;
import com.nibblelab.smartchurch.soundcloud.SoundCloudPlayerView;
import com.nibblelab.smartchurch.ui.events.EstudoListEvents;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.listeners.AbstractYouTubePlayerListener;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.views.YouTubePlayerView;

import java.util.ArrayList;
import java.util.List;

public class EstudoFragment extends BaseFragment implements EstudoListEvents {

    private static final String TAG = "EstudoFragment";
    private EstudoFragment.OnEstudoFragInteractionListener mListener;

    //inputs
    AppCompatSpinner serie;

    // dados
    List<SerieEstudoData> series;

    // adapters (spinners)
    ArrayAdapter<String> serieEstudoDataAdapter;

    // id's
    String id_serie;
    String igreja;

    // lista de estudos
    ScrollView estudosScroll;
    RecyclerView estudosView;
    RecyclerView.LayoutManager estudosLayoutManager;
    EstudoAdapter estudoAdapter;
    List<EstudoData> estudos;

    // estudo
    EstudoData estudo;
    TextView estudoTitulo;
    WebView estudoTexto;
    YouTubePlayerView estudoYoutubeView;
    YouTubePlayer estudoYoutubePlayer;
    VimeoPlayerView estudoVimeoView;
    String idVideo;
    SoundCloudPlayerView estudoAudioView;
    String idAudio;

    // controles
    RelativeLayout estudosListWrp;
    RelativeLayout estudoDataWrp;
    FloatingActionButton estudoBack;
    FloatingActionButton estudoListPrev;
    FloatingActionButton estudoListNext;
    int page = 1;
    int pageSize = 10;

    // flag se tem ou não dados via transferência entre fragmentos
    boolean hasTransfer;

    public EstudoFragment() {
        // Required empty public constructor
    }

    public static EstudoFragment newInstance() {
        EstudoFragment fragment = new EstudoFragment();
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View inf = inflater.inflate(R.layout.fragment_estudo, container, false);

        activity.setToolbarTitle(R.string.menu_estudo);

        igreja = activity.getUser().getMembresia().getIgreja();
        id_serie = "";

        // inputs
        serie = (AppCompatSpinner) inf.findViewById(R.id.serie_estudo_spinner);

        // views
        estudosView = (RecyclerView) inf.findViewById(R.id.estudos_list);
        estudosListWrp = (RelativeLayout) inf.findViewById(R.id.include_estudos);
        estudoDataWrp = (RelativeLayout) inf.findViewById(R.id.include_estudo);
        estudosScroll = (ScrollView) inf.findViewById(R.id.estudos_scroll);

        // sermão
        estudoTitulo = (TextView) inf.findViewById(R.id.estudo_dt_titulo);
        estudoTexto = (WebView) inf.findViewById(R.id.estudo_dt_texto);
        estudoYoutubeView = (YouTubePlayerView) inf.findViewById(R.id.estudo_dt_youtube);
        estudoVimeoView = (VimeoPlayerView) inf.findViewById(R.id.estudo_dt_vimeo);
        estudoAudioView = (SoundCloudPlayerView) inf.findViewById(R.id.estudo_dt_soundcloud);

        estudoTexto.setBackgroundColor(Color.TRANSPARENT);

        this.initYoutube();
        this.initVimeo();
        this.initSoundCloud();

        // controles
        estudoBack = (FloatingActionButton) inf.findViewById(R.id.estudo_back);
        estudoListPrev = (FloatingActionButton) inf.findViewById(R.id.estudos_anterior);
        estudoListNext = (FloatingActionButton) inf.findViewById(R.id.estudos_proximo);

        estudoListPrev.hide();
        estudoListNext.hide();

        // handler do botão de volta do modo exibição para o modo listagem
        estudoBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toEstudosList();
            }
        });

        // handler do botão de próxima página na listagem
        estudoListPrev.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toPrevPage();
            }
        });

        // handler do botão de página anterior na listagem
        estudoListNext.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toNextPage();
            }
        });

        estudosListWrp.setVisibility(View.GONE);
        estudoDataWrp.setVisibility(View.GONE);

        this.getSeriesEstudos();
        this.getEstudos();

        return inf;
    }

    public void setHasTransfer(boolean hasTransfer)
    {
        this.hasTransfer = hasTransfer;
    }

    /*******************************************************
     *      INICIALIZAÇÕES
     *******************************************************/

    /**
     * inicialize o player youtube
     */
    public void initYoutube()
    {
        getLifecycle().addObserver(estudoYoutubeView);
        estudoYoutubeView.addYouTubePlayerListener(new AbstractYouTubePlayerListener() {
            @Override
            public void onReady(@NonNull YouTubePlayer youTubePlayer) {
                activity.hideLoadingSpinner();
                estudoYoutubePlayer = youTubePlayer;
                if (mListener != null) {
                    mListener.onEstudoFragmentFullyLoaded();
                }
                if(!hasTransfer) {
                    toggleListArea(true);
                }
            }
        });
        estudoYoutubeView.setVisibility(View.GONE);
    }

    /**
     * inicialize o player vimeo
     */
    public void initVimeo()
    {
        getLifecycle().addObserver(estudoVimeoView);
        estudoVimeoView.setVisibility(View.GONE);
    }

    /**
     * inicialize o player soundcloud
     */
    public void initSoundCloud()
    {
        getLifecycle().addObserver(estudoAudioView);
        estudoAudioView.setVisibility(View.GONE);
    }


    /*******************************************************
     *      MÉTODOS RELATIVOS A BUSCA DE ESTUDOS
     *******************************************************/

    /**
     * Veja se tem páginas a serem exibidas
     *
     * @param max
     * @return
     */
    private boolean hasNextPage(int max)
    {
        int current = page * pageSize;
        return (current < max);
    }

    /**
     * Vá para a próxima página
     */
    public void toNextPage()
    {
        page++;
        this.getEstudos();
    }

    /**
     * Volte à página anterior
     */
    public void toPrevPage()
    {
        page--;
        this.getEstudos();
    }

    /**
     * Busque as séries de estudo
     *
     */
    public void getSeriesEstudos()
    {
        activity.showLoadingSpinner();
        SerieEstudoAPI api = new SerieEstudoAPI(activity);
        api.getSeriesEstudoAtivos(igreja, new ApiResponse<List<SerieEstudoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<SerieEstudoData> data) {
                activity.hideLoadingSpinner();
                EstudoFragment.this.generateSerieEstudosSpinner(data);
            }

            @Override
            public void onResponse(List<SerieEstudoData> data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro: " + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    /**
     * Gere o select de séries (usado para filtrar os estudos)
     *
     * @param datas
     */
    public void generateSerieEstudosSpinner(List<SerieEstudoData> datas)
    {
        series = datas;
        List<String> lista = new ArrayList<String>();
        int indx = -1;
        int counter = 1;

        lista.add("Filtrar por Série");
        for(SerieEstudoData i : series)
        {
            lista.add(i.getNome());
            if(StringHelper.notEmpty(id_serie) && id_serie.equals(i.getId())) {
                indx = counter;
            }
            counter++;
        }

        serieEstudoDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, lista);
        serieEstudoDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        serie.setAdapter(serieEstudoDataAdapter);

        serie.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String nome = parent.getItemAtPosition(position).toString();
                String serie_selected = SerieEstudoData.getIdByNomeOnList(series, nome);
                if(StringHelper.notEmpty(serie_selected)) {
                    id_serie = serie_selected;
                }
                else {
                    id_serie = "";
                }
                getEstudos();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    /**
     * Busque os estudos
     *
     */
    public void getEstudos()
    {
        activity.showLoadingSpinner();
        EstudoAPI api = new EstudoAPI(activity);
        api.getEstudosAtivosDaIgrejaBySerieAndPage(igreja, id_serie, page, pageSize, new ApiResponse<List<EstudoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<EstudoData> data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<EstudoData> data, int total) {
                activity.hideLoadingSpinner();
                EstudoFragment.this.generateEstudos(data, total);
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro: " + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    /**
     * Gere a lista com os estudos buscados
     *
     * @param datas
     * @param total
     */
    public void generateEstudos(List<EstudoData> datas, int total)
    {
        estudos = datas;
        estudosLayoutManager = new GridLayoutManager(this.getContext(), 2);
        estudosView.setLayoutManager(estudosLayoutManager);

        estudoAdapter = new EstudoAdapter(this.getContext(), estudos,this);
        estudosView.setAdapter(estudoAdapter);

        // veja se é possível exibir o botão de página anterior
        if(page == 1) {
            estudoListPrev.hide();
        }
        else {
            estudoListPrev.show();
        }

        // veja se é possível exibir o botão de próxima página
        if(hasNextPage(total)) {
            estudoListNext.show();
        }
        else {
            estudoListNext.hide();
        }

        // vá para o topo da lista
        estudosScroll.fullScroll(ScrollView.FOCUS_UP);
    }

    /**
     * Troque o view de listagem pelo de dados
     *
     * @param show
     */
    public void toggleListArea(boolean show)
    {
        if(show) {
            this.shutdownPlayers();
            estudosListWrp.setVisibility(View.VISIBLE);
            estudoDataWrp.setVisibility(View.GONE);
        }
        else {
            estudosListWrp.setVisibility(View.GONE);
            estudoDataWrp.setVisibility(View.VISIBLE);
        }
    }

    /*******************************************************
     *      MÉTODOS RELATIVOS A EXIBIÇÃO DE ESTUDO
     *******************************************************/

    /**
     * Pare todos os players
     */
    public void shutdownPlayers()
    {
        if(estudoYoutubePlayer != null) {
            estudoYoutubePlayer.pause();
            estudoYoutubeView.setVisibility(View.GONE);
        }
        if(estudoVimeoView != null) {
            estudoVimeoView.pause();
            estudoVimeoView.setVisibility(View.GONE);
        }
        if(estudoAudioView != null) {
            estudoAudioView.pauseAudio();
            estudoAudioView.setVisibility(View.GONE);
        }
    }

    /**
     * Mostre o player youtube
     */
    private void showYoutubePlayer()
    {
        estudoVimeoView.setVisibility(View.GONE);
        estudoYoutubeView.setVisibility(View.VISIBLE);
    }

    /**
     * Mostre o player vimeo
     */
    private void showVimeoPlayer()
    {
        estudoYoutubeView.setVisibility(View.GONE);
        estudoVimeoView.setVisibility(View.VISIBLE);
    }

    /**
     * Exiba o estudo selecionado pelo usuário na listagem
     *
     * @param object
     */
    @Override
    public void onSelectEstudo(Object object)
    {
        estudo = (EstudoData) object;
        estudoTitulo.setText(estudo.getTitulo());

        // webview para o texto do sermão, que é um texto HTML
        estudoTexto.loadDataWithBaseURL(null, estudo.getConteudo(), "text/html", "utf-8", null);

        // veja se tem vídeo e em caso afirmativo, teste se é youtube ou vimeo
        if(StringHelper.notEmpty(estudo.getVideo())) {
            // tente obter como youtuve
            idVideo = MediaHelper.getYoutubeVideoId(estudo.getVideo());
            if(StringHelper.notEmpty(idVideo)) {
                showYoutubePlayer();
                estudoYoutubePlayer.cueVideo(idVideo, 0);
            }
            else {
                // veja se dá certo como vimeo
                idVideo = MediaHelper.getVimeoVideoId(estudo.getVideo());
                if(StringHelper.notEmpty(idVideo)) {
                    estudoVimeoView.initialize(false, Integer.parseInt(idVideo));
                    showVimeoPlayer();
                }
            }
        }

        // veja se o sermão tem áudio e se está no padrão do soundcloud
        idAudio = MediaHelper.getSoundCloudAudioId(estudo.getAudio());

        if(StringHelper.notEmpty(idAudio)) {
            estudoAudioView.setVisibility(View.VISIBLE);
            estudoAudioView.loadAudio(idAudio);
        }

        // mude a interface de modo listagem para modo exibição
        this.toggleListArea(false);
    }

    /**
     * Limpe os dados do modo exibição
     */
    public void clearEstudoData()
    {
        estudoTitulo.setText("");
        estudoTexto.loadDataWithBaseURL(null, "", "text/html", "utf-8", null);
        estudoAudioView.pauseAudio();
    }

    /**
     * Volte para o modo listagem
     */
    public void toEstudosList()
    {
        this.toggleListArea(true);
        this.clearEstudoData();
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof EstudoFragment.OnEstudoFragInteractionListener) {
            mListener = (EstudoFragment.OnEstudoFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnFragmentInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnEstudoFragInteractionListener {
        void onEstudoFragmentInteraction();
        void onEstudoFragmentFullyLoaded();
    }
}