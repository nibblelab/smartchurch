package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.github.mikephil.charting.animation.Easing;
import com.github.mikephil.charting.charts.PieChart;
import com.github.mikephil.charting.components.Legend;
import com.github.mikephil.charting.data.PieData;
import com.github.mikephil.charting.data.PieDataSet;
import com.github.mikephil.charting.data.PieEntry;
import com.github.mikephil.charting.formatter.PercentFormatter;
import com.nibblelab.smartchurch.API.EstudoAPI;
import com.nibblelab.smartchurch.API.MuralAPI;
import com.nibblelab.smartchurch.API.PessoaAPI;
import com.nibblelab.smartchurch.API.SermaoAPI;
import com.nibblelab.smartchurch.API.TransmissaoAPI;
import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.MuralAdapter;
import com.nibblelab.smartchurch.common.DateHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.EstudoData;
import com.nibblelab.smartchurch.model.MuralData;
import com.nibblelab.smartchurch.model.SermaoData;
import com.nibblelab.smartchurch.model.TransmissaoData;
import com.nibblelab.smartchurch.ui.events.MuralListEvents;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

public class HomeFragment extends BaseFragment implements MuralListEvents {

    private OnHomeFragInteractionListener mListener;

    private static final String TAG = "HomeFragment";
    private static final int daysBefore = 15;

    CardView sermaoCard;
    TextView sermaoCounter;
    TextView sermaoTitle;
    TextView sermaoDesc;

    CardView transmissaoCard;
    TextView transmissaoCounter;
    TextView transmissaoTitle;

    CardView dadosCard;

    CardView perfCard;
    PieChart perfilChart;

    // card com o último sermão
    CardView lastSermaoCard;
    ImageView lastSermaoImg;
    TextView lastSermaoTitulo;
    SermaoData lastSermao;

    // card com o último estudo
    CardView lastEstudoCard;
    ImageView lastEstudoImg;
    TextView lastEstudoTitulo;
    EstudoData lastEstudo;

    // mural
    RecyclerView muralView;
    RecyclerView.LayoutManager muralLayoutManager;
    MuralAdapter muralAdapter;

    String igreja;
    String id;
    String apos;

    // controle
    ScrollView scroll;

    public HomeFragment() {
        // Required empty public constructor
    }

    public static HomeFragment newInstance() {
        HomeFragment fragment = new HomeFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
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
        // Inflate the layout for this fragment
        View inf = inflater.inflate(R.layout.fragment_home, container, false);

        activity.setToolbarTitle(R.string.app_name);

        // pegue o id da igreja
        igreja = activity.getUser().getMembresia().getIgreja();
        // pegue o id do usuário
        id = activity.getUser().getId();
        // pegue a data de duas semanas atrás
        apos = DateHelper.fromDateToStringDate(DateHelper.dateFromDays(daysBefore));

        sermaoCard = (CardView) inf.findViewById(R.id.sermao_card);
        sermaoCounter = (TextView) inf.findViewById(R.id.sermao_count);
        sermaoTitle = (TextView) inf.findViewById(R.id.home_sermao_title);
        sermaoDesc = (TextView) inf.findViewById(R.id.home_sermao_desc);

        transmissaoCard = (CardView) inf.findViewById(R.id.transmissao_card);
        transmissaoCounter = (TextView) inf.findViewById(R.id.transmissao_count);
        transmissaoTitle = (TextView) inf.findViewById(R.id.home_transmissao_title);

        dadosCard = (CardView) inf.findViewById(R.id.dados_card);

        perfCard = (CardView) inf.findViewById(R.id.perf_graph_card);
        perfilChart = (PieChart) inf.findViewById(R.id.chart_perfil);

        lastSermaoCard = (CardView) inf.findViewById(R.id.last_sermao_card);
        lastSermaoImg = (ImageView) inf.findViewById(R.id.card_sermao_img);
        lastSermaoTitulo = (TextView) inf.findViewById(R.id.card_sermao_titulo);

        lastEstudoCard = (CardView) inf.findViewById(R.id.last_estudo_card);
        lastEstudoImg = (ImageView) inf.findViewById(R.id.card_estudo_img);
        lastEstudoTitulo = (TextView) inf.findViewById(R.id.card_estudo_titulo);

        muralView = (RecyclerView) inf.findViewById(R.id.mural_list);

        scroll = (ScrollView) inf.findViewById(R.id.scroll_home);

        sermaoCard.setVisibility(View.GONE);
        transmissaoCard.setVisibility(View.GONE);
        dadosCard.setVisibility(View.GONE);
        perfCard.setVisibility(View.GONE);
        lastSermaoCard.setVisibility(View.GONE);
        lastEstudoCard.setVisibility(View.GONE);
        muralView.setVisibility(View.GONE);

        if(StringHelper.notEmpty(igreja)) {
            // há informação de membresia. Veja se o módulo de igreja está ativo
            if(activity.userHasModulo("MOD_IGREJA")) {
                this.getSermoesCountAfter();
            }
            else {
                // gere apenas o gráfico de preenchimento do perfil
                this.getPercentPerfilPreenchido();
            }
        }
        else {
            // não há informação de membresia. Carregue apenas o gráfico de perfil
            this.warnCadastro();
        }

        return inf;
    }

    /**
     * Avise o usuário para completar o cadastro de membresia dele
     */
    public void warnCadastro()
    {
        dadosCard.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (mListener != null) {
                    mListener.onHomeToMembresia();
                }
            }
        });
        dadosCard.setVisibility(View.VISIBLE);

        // gere o gráfico de preenchimento do perfil
        this.getPercentPerfilPreenchido();
    }

    /**
     * Verifique se há sermões publicados nas duas últimas semanas
     */
    public void getSermoesCountAfter()
    {
        activity.showLoadingSpinner();
        SermaoAPI api = new SermaoAPI(activity);
        api.getSermoesAtivosPublicadosApos(igreja, apos, new ApiResponse<List<SermaoData>>() {
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
                activity.hideLoadingSpinner();
                HomeFragment.this.countLastSermoes(total);
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

    public void countLastSermoes(int total)
    {
        if(total > 0) {
            sermaoCounter.setText("" + total);
            if(total == 1) {
                sermaoTitle.setText(R.string.h_sermao_title_s);
                sermaoDesc.setText(R.string.h_sermao_desc_s);
            }
            else {
                sermaoTitle.setText(R.string.h_sermao_title);
                sermaoDesc.setText(R.string.h_sermao_desc);
            }

            sermaoCard.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mListener != null) {
                        mListener.onHomeToPalavra();
                    }
                }
            });

            sermaoCard.setVisibility(View.VISIBLE);
        }

        // veja se há transmissões ao vivo agora
        this.getTransmissoes();
    }

    /********************************************************
     *           TRANSMISSÕES
     ********************************************************/

    /**
     * Verifique se há transmissões ao vivo agora
     */
    public void getTransmissoes()
    {
        activity.showLoadingSpinner();
        TransmissaoAPI api = new TransmissaoAPI(activity);
        api.getTransmissoesAtivasDaIgreja(igreja, new ApiResponse<List<TransmissaoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<TransmissaoData> data) {
                activity.hideLoadingSpinner();
                HomeFragment.this.countTransmissoes(data);
            }

            @Override
            public void onResponse(List<TransmissaoData> data, int total) {
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

    public void countTransmissoes(List<TransmissaoData> data)
    {
        int total = data.size();
        if(total > 0) {
            transmissaoCounter.setText("" + total);
            if(total == 1) {
                transmissaoTitle.setText(R.string.h_transmissao_title_s);
            }
            else {
                transmissaoTitle.setText(R.string.h_transmissao_title);
            }

            transmissaoCard.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mListener != null) {
                        mListener.onHomeToTransmissao();
                    }
                }
            });

            transmissaoCard.setVisibility(View.VISIBLE);
        }

        // busque o último sermão
        this.getLastSermao();
    }

    /******************************
     *           MURAL
     ******************************/

    /**
     * Busque o último sermão publicado
     */
    public void getLastSermao()
    {
        activity.showLoadingSpinner();
        SermaoAPI api = new SermaoAPI(activity);
        api.getLastSermoaoAtivo(igreja, new ApiResponse<List<SermaoData>>() {
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
                HomeFragment.this.renderLastSermao(data, total);
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

    public void renderLastSermao(List<SermaoData> data, int total)
    {
        if(total > 0) {
            lastSermao = data.get(0);

            if(StringHelper.notEmpty(lastSermao.getLogoApp()))
            {
                Picasso.get().load(ApiContants.RC_URL + "/" + lastSermao.getLogoApp()).into(lastSermaoImg);
            }
            else if(StringHelper.notEmpty(lastSermao.getLogo()))
            {
                Picasso.get().load(ApiContants.RC_URL + "/" + lastSermao.getLogo()).into(lastSermaoImg);
            }

            lastSermaoTitulo.setText(lastSermao.getTitulo());

            lastSermaoCard.setOnClickListener(v -> {
                if (mListener != null) {
                    mListener.onHomeToPalavraData(lastSermao);
                }
            });

            lastSermaoCard.setVisibility(View.VISIBLE);
        }

        getLastEstudo();
    }

    /**
     * Busque o último estudo
     *
     */
    public void getLastEstudo()
    {
        activity.showLoadingSpinner();
        EstudoAPI api = new EstudoAPI(activity);
        api.getLastEstudoAtivo(igreja, new ApiResponse<List<EstudoData>>() {
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
                HomeFragment.this.renderLastEstudo(data, total);
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

    public void renderLastEstudo(List<EstudoData> data, int total)
    {
        if(total > 0) {
            lastEstudo = data.get(0);

            if(StringHelper.notEmpty(lastEstudo.getLogoApp()))
            {
                Picasso.get().load(ApiContants.RC_URL + "/" + lastEstudo.getLogoApp()).into(lastEstudoImg);
            }
            else if(StringHelper.notEmpty(lastEstudo.getLogo()))
            {
                Picasso.get().load(ApiContants.RC_URL + "/" + lastEstudo.getLogo()).into(lastEstudoImg);
            }

            lastEstudoTitulo.setText(lastEstudo.getTitulo());

            lastEstudoCard.setOnClickListener(v -> {
                if (mListener != null) {
                    mListener.onHomeToEstudoData(lastEstudo);
                }
            });

            lastEstudoCard.setVisibility(View.VISIBLE);
        }

        getMural();
    }

    /**
     * Busque o mural
     */
    public void getMural()
    {
        activity.showLoadingSpinner();
        MuralAPI api = new MuralAPI(activity);
        api.getMuraisAtivosDaIgrejaEPessoa(igreja, activity.getUser().getId(), new ApiResponse<List<MuralData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<MuralData> data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<MuralData> data, int total) {
                activity.hideLoadingSpinner();
                HomeFragment.this.renderMural(data, total);
            }

            @Override
            public void onAlreadyExecuted() {

            }

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

    public void renderMural(List<MuralData> data, int total)
    {
        if(total > 0) {
            muralLayoutManager = new LinearLayoutManager(this.getContext());
            muralView.setLayoutManager(muralLayoutManager);

            muralAdapter = new MuralAdapter(this.getContext(), data,this, getLifecycle(), activity);
            muralView.setAdapter(muralAdapter);

            // ajuste a altura
            RelativeLayout.LayoutParams lp = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT);
            muralView.setLayoutParams(lp);

            muralView.setVisibility(View.VISIBLE);
        }

        scroll.smoothScrollTo(0,0);

        // gere o gráfico de preenchimento do perfil
        this.getPercentPerfilPreenchido();
    }

    @Override
    public void onAddToBookmark(Object object) {

    }

    @Override
    public void onRemoveFromBookmark() {

    }


    /**************************************
     *               GRÁFICOS
     **************************************/

    /**
     * Gere o gráfico com o percentual do perfil preenchido
     */
    public void getPercentPerfilPreenchido()
    {
        activity.showLoadingSpinner();
        PessoaAPI api = new PessoaAPI(activity);
        api.getPerfilPreenchido(id, new ApiResponse<Float>() {

            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(Float data) {
                activity.hideLoadingSpinner();
                HomeFragment.this.generatePerfGraph(data);
            }

            @Override
            public void onResponse(Float data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() {
                activity.hideLoadingSpinner();
            }

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

    public void generatePerfGraph(Float percent)
    {
        ArrayList<PieEntry> values = new ArrayList<>();
        ArrayList<Integer> colors = new ArrayList<>();
        Float remain = 1 - percent;
        PieDataSet dataSet;
        PieData data;
        Legend l;

        if(remain > 0f) {
            perfCard.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mListener != null) {
                        mListener.onHomeToPerfil();
                    }
                }
            });
        }

        perfCard.setVisibility(View.VISIBLE);

        // configure o gráfico
        perfilChart.setBackgroundColor(Color.WHITE);
        perfilChart.setUsePercentValues(true);
        perfilChart.getDescription().setEnabled(false);

        perfilChart.setDrawHoleEnabled(true);
        perfilChart.setHoleColor(Color.WHITE);

        perfilChart.setTransparentCircleColor(Color.WHITE);
        perfilChart.setTransparentCircleAlpha(110);

        perfilChart.setHoleRadius(58f);
        perfilChart.setTransparentCircleRadius(61f);

        perfilChart.setDrawCenterText(true);

        perfilChart.setRotationEnabled(false);
        perfilChart.setHighlightPerTapEnabled(true);

        perfilChart.setCenterTextOffset(0, -20);

        values.add(new PieEntry(percent, "Preenchido"));
        colors.add(Color.rgb(73, 182, 214));

        if(remain > 0f) {
            values.add(new PieEntry(remain, "Não Preenchido"));
            colors.add(Color.rgb(255, 91, 87));
        }

        dataSet = new PieDataSet(values, "");
        dataSet.setColors(colors);
        dataSet.setSliceSpace(3f);
        dataSet.setSelectionShift(5f);

        data = new PieData(dataSet);
        data.setValueFormatter(new PercentFormatter(perfilChart));
        data.setValueTextSize(11f);
        data.setValueTextColor(Color.rgb(33, 37, 41));

        perfilChart.setData(data);

        perfilChart.invalidate();

        perfilChart.animateY(1400, Easing.EaseInOutQuad);

        l = perfilChart.getLegend();
        l.setVerticalAlignment(Legend.LegendVerticalAlignment.TOP);
        l.setHorizontalAlignment(Legend.LegendHorizontalAlignment.CENTER);
        l.setOrientation(Legend.LegendOrientation.HORIZONTAL);
        l.setDrawInside(false);
        l.setXEntrySpace(7f);
        l.setYEntrySpace(0f);
        l.setYOffset(0f);

        perfilChart.setEntryLabelColor(Color.rgb(33, 37, 41));
        perfilChart.setEntryLabelTextSize(12f);

    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onHomeFragInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnHomeFragInteractionListener) {
            mListener = (OnHomeFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnHomeFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnHomeFragInteractionListener {
        void onHomeFragInteraction();
        void onHomeToPerfil();
        void onHomeToMembresia();
        void onHomeToPalavra();
        void onHomeToTransmissao();
        void onHomeToPalavraData(Object data);
        void onHomeToEstudoData(Object data);
    }

}
