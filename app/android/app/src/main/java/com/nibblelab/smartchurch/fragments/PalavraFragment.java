package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;

import com.ct7ct7ct7.androidvimeoplayer.view.VimeoPlayerView;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.AppCompatSpinner;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
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

import com.nibblelab.smartchurch.API.SerieSermaoAPI;
import com.nibblelab.smartchurch.API.SermaoAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.SermaoAdapter;
import com.nibblelab.smartchurch.common.MediaHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.SerieSermaoData;
import com.nibblelab.smartchurch.model.SermaoData;
import com.nibblelab.smartchurch.soundcloud.SoundCloudPlayerView;
import com.nibblelab.smartchurch.ui.events.SermaoListEvents;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.YouTubePlayer;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.listeners.AbstractYouTubePlayerListener;
import com.pierfrancescosoffritti.androidyoutubeplayer.core.player.views.YouTubePlayerView;

import java.util.ArrayList;
import java.util.List;


public class PalavraFragment extends BaseFragment implements SermaoListEvents {

    private static final String TAG = "PalavraFragment";
    private OnPalavraFragInteractionListener mListener;

    //inputs
    AppCompatSpinner serie;

    // dados
    List<SerieSermaoData> series;

    // adapters (spinners)
    ArrayAdapter<String> serieSermaoDataAdapter;

    // id's
    String id_serie;
    String igreja;

    // lista de sermões
    ScrollView sermoesScroll;
    RecyclerView sermoesView;
    RecyclerView.LayoutManager sermoesLayoutManager;
    SermaoAdapter sermaoAdapter;
    List<SermaoData> sermoes;

    // sermão
    SermaoData sermao;
    TextView sermaoTitulo;
    WebView sermaoTexto;
    YouTubePlayerView sermaoYoutubeView;
    YouTubePlayer sermaoYoutubePlayer;
    VimeoPlayerView sermaoVimeoView;
    String idVideo;
    SoundCloudPlayerView sermaoAudioView;
    String idAudio;

    // controles
    RelativeLayout sermoesListWrp;
    RelativeLayout sermaoDataWrp;
    FloatingActionButton sermaoBack;
    FloatingActionButton sermaoListPrev;
    FloatingActionButton sermaoListNext;
    int page = 1;
    int pageSize = 10;

    // flag se tem ou não dados via transferência entre fragmentos
    boolean hasTransfer;

    public PalavraFragment() {
        // Required empty public constructor
    }

    public static PalavraFragment newInstance() {
        PalavraFragment fragment = new PalavraFragment();
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
        View inf = inflater.inflate(R.layout.fragment_palavra, container, false);

        activity.setToolbarTitle(R.string.menu_palavra);

        igreja = activity.getUser().getMembresia().getIgreja();
        id_serie = "";

        // inputs
        serie = (AppCompatSpinner) inf.findViewById(R.id.serie_spinner);

        // views
        sermoesView = (RecyclerView) inf.findViewById(R.id.sermoes_list);
        sermoesListWrp = (RelativeLayout) inf.findViewById(R.id.include_sermoes);
        sermaoDataWrp = (RelativeLayout) inf.findViewById(R.id.include_sermao);
        sermoesScroll = (ScrollView) inf.findViewById(R.id.sermoes_scroll);

        // sermão
        sermaoTitulo = (TextView) inf.findViewById(R.id.sermao_dt_titulo);
        sermaoTexto = (WebView) inf.findViewById(R.id.sermao_dt_texto);
        sermaoYoutubeView = (YouTubePlayerView) inf.findViewById(R.id.sermao_dt_youtube);
        sermaoVimeoView = (VimeoPlayerView) inf.findViewById(R.id.sermao_dt_vimeo);
        sermaoAudioView = (SoundCloudPlayerView) inf.findViewById(R.id.sermao_dt_soundcloud);

        sermaoTexto.setBackgroundColor(Color.TRANSPARENT);

        this.initYoutube();
        this.initVimeo();
        this.initSoundCloud();

        // controles
        sermaoBack = (FloatingActionButton) inf.findViewById(R.id.pv_back);
        sermaoListPrev = (FloatingActionButton) inf.findViewById(R.id.sermoes_anterior);
        sermaoListNext = (FloatingActionButton) inf.findViewById(R.id.sermoes_proximo);

        sermaoListPrev.hide();
        sermaoListNext.hide();

        // handler do botão de volta do modo exibição para o modo listagem
        sermaoBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toSermoesList();
            }
        });

        // handler do botão de próxima página na listagem
        sermaoListPrev.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toPrevPage();
            }
        });

        // handler do botão de página anterior na listagem
        sermaoListNext.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toNextPage();
            }
        });

        sermoesListWrp.setVisibility(View.GONE);
        sermaoDataWrp.setVisibility(View.GONE);

        this.getSeriesSermoes();

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
        getLifecycle().addObserver(sermaoYoutubeView);
        sermaoYoutubeView.addYouTubePlayerListener(new AbstractYouTubePlayerListener() {
            @Override
            public void onReady(@NonNull YouTubePlayer youTubePlayer) {
                activity.hideLoadingSpinner();
                sermaoYoutubePlayer = youTubePlayer;
                if (mListener != null) {
                    mListener.onPalavraFragmentFullyLoaded();
                }
                if(!hasTransfer) {
                    toggleListArea(true);
                }
            }
        });
        sermaoYoutubeView.setVisibility(View.GONE);
    }

    /**
     * inicialize o player vimeo
     */
    public void initVimeo()
    {
        getLifecycle().addObserver(sermaoVimeoView);
        sermaoVimeoView.setVisibility(View.GONE);
    }

    /**
     * inicialize o player soundcloud
     */
    public void initSoundCloud()
    {
        getLifecycle().addObserver(sermaoAudioView);
        sermaoAudioView.setVisibility(View.GONE);
    }

    /*******************************************************
     *      MÉTODOS RELATIVOS A BUSCA DE SERMÕES
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
        this.getSermoes();
    }

    /**
     * Volte à página anterior
     */
    public void toPrevPage()
    {
        page--;
        this.getSermoes();
    }

    /**
     * Busque as séries de sermão
     *
     */
    public void getSeriesSermoes()
    {
        activity.showLoadingSpinner();
        SerieSermaoAPI api = new SerieSermaoAPI(activity);
        api.getSeriesSermaoAtivos(igreja, new ApiResponse<List<SerieSermaoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<SerieSermaoData> data) {
                PalavraFragment.this.generateSerieSermoesSpinner(data);
            }

            @Override
            public void onResponse(List<SerieSermaoData> data, int total) {
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
     * Gere o select de séries (usado para filtrar os sermões)
     *
     * @param datas
     */
    public void generateSerieSermoesSpinner(List<SerieSermaoData> datas)
    {
        series = datas;
        List<String> lista = new ArrayList<String>();
        int indx = -1;
        int counter = 1;

        lista.add("Filtrar por Série");
        for(SerieSermaoData i : series)
        {
            lista.add(i.getNome());
            if(StringHelper.notEmpty(id_serie) && id_serie.equals(i.getId())) {
                indx = counter;
            }
            counter++;
        }

        serieSermaoDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, lista);
        serieSermaoDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        serie.setAdapter(serieSermaoDataAdapter);

        serie.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String nome = parent.getItemAtPosition(position).toString();
                String serie_selected = SerieSermaoData.getIdByNomeOnList(series, nome);
                if(StringHelper.notEmpty(serie_selected)) {
                    id_serie = serie_selected;
                }
                else {
                    id_serie = "";
                }
                getSermoes();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        this.getSermoes();
    }

    /**
     * Busque os sermões
     *
     */
    public void getSermoes()
    {
        SermaoAPI api = new SermaoAPI(activity);
        api.getSermoesAtivosOfSerieByPage(igreja, id_serie, page, pageSize, new ApiResponse<List<SermaoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<SermaoData> data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<SermaoData> data, int total) {
                PalavraFragment.this.generateSermoes(data, total);
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
     * Gere a lista com os sermões buscados
     *
     * @param datas
     * @param total
     */
    public void generateSermoes(List<SermaoData> datas, int total)
    {
        sermoes = datas;
        sermoesLayoutManager = new GridLayoutManager(this.getContext(), 2);
        sermoesView.setLayoutManager(sermoesLayoutManager);

        sermaoAdapter = new SermaoAdapter(this.getContext(), sermoes,this);
        sermoesView.setAdapter(sermaoAdapter);

        // veja se é possível exibir o botão de página anterior
        if(page == 1) {
            sermaoListPrev.hide();
        }
        else {
            sermaoListPrev.show();
        }

        // veja se é possível exibir o botão de próxima página
        if(hasNextPage(total)) {
            sermaoListNext.show();
        }
        else {
            sermaoListNext.hide();
        }

        // vá para o topo da lista
        sermoesScroll.fullScroll(ScrollView.FOCUS_UP);
    }

    /**
     * Troque o view de listagem pelo de dados ou vice versa
     *
     * @param show
     */
    public void toggleListArea(boolean show)
    {
        if(show) {
            sermoesListWrp.setVisibility(View.VISIBLE);
            sermaoDataWrp.setVisibility(View.GONE);
            this.shutdownPlayers();
        }
        else {
            sermoesListWrp.setVisibility(View.GONE);
            sermaoDataWrp.setVisibility(View.VISIBLE);
        }
    }

    /*******************************************************
     *      MÉTODOS RELATIVOS A EXIBIÇÃO DE SERMÃO
     *******************************************************/

    /**
     * Pare todos os players
     */
    public void shutdownPlayers()
    {
        if(sermaoYoutubePlayer != null) {
            sermaoYoutubePlayer.pause();
            sermaoYoutubeView.setVisibility(View.GONE);
        }
        if(sermaoVimeoView != null) {
            sermaoVimeoView.pause();
            sermaoVimeoView.setVisibility(View.GONE);
        }
        if(sermaoAudioView != null) {
            sermaoAudioView.pauseAudio();
            sermaoAudioView.setVisibility(View.GONE);
        }
    }

    /**
     * Mostre o player youtube
     */
    private void showYoutubePlayer()
    {
        sermaoVimeoView.setVisibility(View.GONE);
        sermaoYoutubeView.setVisibility(View.VISIBLE);
    }

    /**
     * Mostre o player vimeo
     */
    private void showVimeoPlayer()
    {
        sermaoYoutubeView.setVisibility(View.GONE);
        sermaoVimeoView.setVisibility(View.VISIBLE);
    }

    /**
     * Exiba o sermão selecionado pelo usuário na listagem
     *
     * @param object
     */
    @Override
    public void onSelectSermao(Object object)
    {
        // mude a interface de modo listagem para modo exibição
        this.toggleListArea(false);

        sermao = (SermaoData) object;
        sermaoTitulo.setText(sermao.getTitulo());

        // webview para o texto do sermão, que é um texto HTML
        sermaoTexto.loadDataWithBaseURL(null, sermao.getConteudo(), "text/html", "utf-8", null);

        // veja se tem vídeo e em caso afirmativo, teste se é youtube ou vimeo
        if(StringHelper.notEmpty(sermao.getVideo())) {
            // tente obter como youtube
            idVideo = MediaHelper.getYoutubeVideoId(sermao.getVideo());
            if(StringHelper.notEmpty(idVideo)) {
                showYoutubePlayer();
                sermaoYoutubePlayer.cueVideo(idVideo, 0);
            }
            else {
                // veja se dá certo como vimeo
                idVideo = MediaHelper.getVimeoVideoId(sermao.getVideo());
                if(StringHelper.notEmpty(idVideo)) {
                    sermaoVimeoView.initialize(false, Integer.parseInt(idVideo));
                    showVimeoPlayer();
                }
            }
        }

        // veja se o sermão tem áudio e se está no padrão do soundcloud
        idAudio = MediaHelper.getSoundCloudAudioId(sermao.getAudio());

        if(StringHelper.notEmpty(idAudio)) {
            sermaoAudioView.setVisibility(View.VISIBLE);
            sermaoAudioView.loadAudio(idAudio);
        }

    }

    /**
     * Limpe os dados do modo exibição
     */
    public void clearSermaoData()
    {
        sermaoTitulo.setText("");
        sermaoTexto.loadDataWithBaseURL(null, "", "text/html", "utf-8", null);
    }

    /**
     * Volte para o modo listagem
     */
    public void toSermoesList()
    {
        this.toggleListArea(true);
        this.clearSermaoData();
    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onPalavraFragmentInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnPalavraFragInteractionListener) {
            mListener = (OnPalavraFragInteractionListener) context;
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


    public interface OnPalavraFragInteractionListener {
        void onPalavraFragmentInteraction();
        void onPalavraFragmentFullyLoaded();
    }
}
