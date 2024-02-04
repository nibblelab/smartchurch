package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;

import androidx.appcompat.widget.AppCompatSpinner;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.nibblelab.smartchurch.API.MembroAPI;
import com.nibblelab.smartchurch.API.PresbiterioAPI;
import com.nibblelab.smartchurch.API.SinodoAPI;
import com.nibblelab.smartchurch.API.TemploAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DialogHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.MembresiaData;
import com.nibblelab.smartchurch.model.MembroData;
import com.nibblelab.smartchurch.model.PresbiterioData;
import com.nibblelab.smartchurch.model.SinodoData;
import com.nibblelab.smartchurch.model.TemploData;

import java.util.ArrayList;
import java.util.List;


public class MembresiaFragment extends BaseFragment {

    private static final String TAG = "MembresiaFragment";
    private OnMembresiaFraInteractionListener mListener;

    //inputs
    AppCompatSpinner sinodo;
    AppCompatSpinner presbiterio;
    AppCompatSpinner igreja;

    // dados
    List<SinodoData> sinodos;
    List<PresbiterioData> presbiterios;
    List<TemploData> igrejas;

    // adapters (spinners)
    ArrayAdapter<String> sinodoDataAdapter;
    ArrayAdapter<String> presbiterioDataAdapter;
    ArrayAdapter<String> igrejaDataAdapter;

    // área de membresia
    LinearLayout membresia_area;
    ImageView img_membresia;
    TextView txt_membresia;

    // botão
    Button save;

    // id's
    public String id_sinodo;
    public String id_presbiterio;
    public String id_igreja;

    MembresiaData membresia;
    MembroData membro;

    boolean changeMembresia = false;

    public MembresiaFragment() {
        // Required empty public constructor
    }

    public static MembresiaFragment newInstance() {
        MembresiaFragment fragment = new MembresiaFragment();
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
        View inf = inflater.inflate(R.layout.fragment_membresia, container, false);

        activity.setToolbarTitle(R.string.menu_membro);

        // inputs
        sinodo = (AppCompatSpinner) inf.findViewById(R.id.m_sinodo_fld);
        presbiterio = (AppCompatSpinner) inf.findViewById(R.id.m_presbiterio_fld);
        igreja = (AppCompatSpinner) inf.findViewById(R.id.m_igreja_fld);

        membresia_area = (LinearLayout) inf.findViewById(R.id.membresia_area);
        img_membresia = (ImageView) inf.findViewById(R.id.img_membresia_stat);
        txt_membresia = (TextView) inf.findViewById(R.id.membresia_txt);

        this.disableMembresiaArea();

        save = (Button) inf.findViewById(R.id.memb_save);
        save.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                MembresiaFragment.this.saveMembresia();
            }
        });

        if(activity.getUser().getMembresia().hasData())
        {
            // usuário tem dados de membresia. Carregue
            membresia = activity.getUser().getMembresia();
            id_igreja = membresia.getIgreja();
            id_presbiterio = membresia.getPresbiterio();
            id_sinodo = membresia.getSinodo();
            this.getMembro();
        }
        else
        {
            this.getSinodos();
        }

        return inf;
    }

    /**
     * Busque os dados do membro
     *
     */
    public void getMembro()
    {
        activity.showLoadingSpinner();
        MembroAPI api = new MembroAPI(activity);
        api.getMe(membresia.getId(), new ApiResponse<MembroData>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(MembroData data) {
                membro = data;
                getSinodos();
            }

            @Override
            public void onResponse(MembroData data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() {
            }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro :" + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }

        });
    }

    /**
     * Busque os sínodos
     */
    public void getSinodos()
    {
        SinodoAPI api = new SinodoAPI(activity);
        api.getSinodosAtivos(new ApiResponse<List<SinodoData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<SinodoData> data) {
                activity.hideLoadingSpinner();
                MembresiaFragment.this.generateSinodoSpinner(data);
            }

            @Override
            public void onResponse(List<SinodoData> data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro :" + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    /**
     * Gere o select de sínodos
     *
     * @param datas
     */
    public void generateSinodoSpinner(List<SinodoData> datas)
    {
        sinodos = datas;
        List<String> lista = new ArrayList<String>();
        int indx = -1;
        int counter = 1;

        lista.add("Selecione");
        for(SinodoData i : sinodos)
        {
            lista.add(i.getNome());
            if(StringHelper.notEmpty(id_sinodo) && id_sinodo.equals(i.getId())) {
                indx = counter;
            }
            counter++;
        }

        sinodoDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, lista);
        sinodoDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        sinodo.setAdapter(sinodoDataAdapter);

        sinodo.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String nome = parent.getItemAtPosition(position).toString();
                String sinodo_selected = SinodoData.getIdByNomeOnList(sinodos, nome);
                if(StringHelper.notEmpty(sinodo_selected)) {
                    if(StringHelper.isDiff(id_sinodo, sinodo_selected)) {
                        // houve alteração no sínodo. Desabilite a área de membresia
                        disableMembresiaArea();
                        changeMembresia = true;
                    }
                    id_sinodo = sinodo_selected;
                    getPresbiterios();
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        if(indx > -1) {
            sinodo.setSelection(indx);
        }
    }

    /**
     * Busque os presbitérios
     */
    public void getPresbiterios()
    {
        activity.showLoadingSpinner();
        PresbiterioAPI api = new PresbiterioAPI(activity);
        api.getPresbiteriosAtivosDoSinodo(id_sinodo, new ApiResponse<List<PresbiterioData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<PresbiterioData> data) {
                activity.hideLoadingSpinner();
                MembresiaFragment.this.generatePresbiterioSpinner(data);
            }

            @Override
            public void onResponse(List<PresbiterioData> data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro :" + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    /**
     * Gere o select de presbitérios
     *
     * @param datas
     */
    public void generatePresbiterioSpinner(List<PresbiterioData> datas)
    {
        presbiterios = datas;
        List<String> lista = new ArrayList<String>();
        int indx = -1;
        int counter = 1;

        lista.add("Selecione");
        for(PresbiterioData i : presbiterios)
        {
            lista.add(i.getNome());
            if(StringHelper.notEmpty(id_presbiterio) && id_presbiterio.equals(i.getId())) {
                indx = counter;
            }
            counter++;
        }

        presbiterioDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, lista);
        presbiterioDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        presbiterio.setAdapter(presbiterioDataAdapter);

        presbiterio.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String nome = parent.getItemAtPosition(position).toString();
                String prebiterio_selected = PresbiterioData.getIdByNomeOnList(presbiterios, nome);
                if(StringHelper.notEmpty(prebiterio_selected)) {
                    if(StringHelper.isDiff(id_presbiterio , prebiterio_selected)) {
                        // houve alteração no presbitério. Desabilite a área de membresia
                        disableMembresiaArea();
                        changeMembresia = true;
                    }
                    id_presbiterio  = prebiterio_selected;
                    getTemplos();
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        if(indx > -1) {
            presbiterio.setSelection(indx);
        }
    }

    /**
     * Busque os templos
     */
    public void getTemplos()
    {
        activity.showLoadingSpinner();
        TemploAPI api = new TemploAPI(activity);
        api.getTemplosAtivosDoPresbiterioESinodo(id_sinodo, id_presbiterio, new ApiResponse<List<TemploData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<TemploData> data) {
                activity.hideLoadingSpinner();
                MembresiaFragment.this.generateTemploSpinner(data);
            }

            @Override
            public void onResponse(List<TemploData> data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro :" + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    /**
     * Gere o select de templo
     *
     * @param datas
     */
    public void generateTemploSpinner(List<TemploData> datas)
    {
        igrejas = datas;
        List<String> lista = new ArrayList<String>();
        int indx = -1;
        int counter = 1;

        lista.add("Selecione");
        for(TemploData i : igrejas)
        {
            lista.add(i.getNome());
            if(StringHelper.notEmpty(id_igreja) && id_igreja.equals(i.getId())) {
                indx = counter;
            }
            counter++;
        }

        igrejaDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, lista);
        igrejaDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        igreja.setAdapter(igrejaDataAdapter);

        igreja.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String nome = parent.getItemAtPosition(position).toString();
                String igreja_selected = TemploData.getIdByNomeOnList(igrejas, nome);
                if(StringHelper.notEmpty(igreja_selected)) {
                    if(StringHelper.isDiff(id_igreja , igreja_selected)) {
                        // houve alteração no templo. Desabilite a área de membresia
                        disableMembresiaArea();
                    }
                    id_igreja = igreja_selected;
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        if(indx > -1) {
            igreja.setSelection(indx);
        }

        if(!changeMembresia)
        {
            this.drawMembresiaArea();
        }
    }

    /**
     * Mostre o status da membresia
     */
    public void drawMembresiaArea()
    {
        if(membro.isArrolado())
        {
            // membro arrolado
            if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                membresia_area.setBackgroundColor(getContext().getColor(R.color.member));
            }
            else {
                membresia_area.setBackgroundColor(Color.parseColor("#3c763d"));
            }

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                img_membresia.setImageDrawable(getResources().getDrawable(R.drawable.ic_check, getContext().getTheme()));
            }
            else {
                img_membresia.setImageDrawable(getResources().getDrawable(R.drawable.ic_check));
            }

            txt_membresia.setText(R.string.m_membresia_membro);
        }
        else
        {
            // membro não arrolado = visitante
            if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                membresia_area.setBackgroundColor(getContext().getColor(R.color.visitor));
            }
            else {
                membresia_area.setBackgroundColor(Color.parseColor("#ff8c1a"));
            }

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                img_membresia.setImageDrawable(getResources().getDrawable(R.drawable.ic_tag, getContext().getTheme()));
            }
            else {
                img_membresia.setImageDrawable(getResources().getDrawable(R.drawable.ic_tag));
            }

            txt_membresia.setText(R.string.m_membresia_visita);
        }

        membresia_area.setVisibility(View.VISIBLE);

        this.checkAlteracaoArrolamento();
    }

    /**
     * Trata o caso de discrepância entre a informação de membresia obtida no login e a de membro, obtida agora
     */
    public void checkAlteracaoArrolamento() {
        if(membresia.isArrolado() != membro.isArrolado()) {
            // há discrepância entre os dados de membresia e de membro. Faça um novo login pra sincronizar!
            activity.warningDialog("Aviso", "Sua membresia foi alterada pela igreja desde seu último acesso. "+
                    "É necessário refazer o login para atualizar os dados.", new DialogHelper() {
                @Override
                public void onCancel() {

                }

                @Override
                public void onOk() {
                    activity.doLogout();
                }
            });
        }
    }

    /**
     * Esconda a área com o status de membresia
     */
    public void disableMembresiaArea()
    {
        membresia_area.setVisibility(View.GONE);
    }

    /**
     * Salve as alterações
     */
    public void saveMembresia()
    {
        if(!StringHelper.notEmpty(id_sinodo) || !StringHelper.notEmpty(id_presbiterio) || !StringHelper.notEmpty(id_igreja))
        {
            activity.errDialog("Erro", "Os campos são obrigatórios!");
            return;
        }

        boolean isCreate = false;
        if(!activity.getUser().getMembresia().hasData() || changeMembresia)
        {
            // criação de dados de membro com opções default
            isCreate = true;
            membro = new MembroData();
            membro.setPessoa(activity.getUser().getId());
            membro.setComungante(false);
            membro.setEspecial(false);
            membro.setArrolado(false);
            membro.setData_admissao("");
            membro.setData_demissao("");
        }

        membro.setIgreja(id_igreja);

        activity.showLoadingSpinner();
        MembroAPI api = new MembroAPI(activity);
        ApiResponse<Object> r = new ApiResponse<Object>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
                activity.successDialog("Sucesso", "Membresia salva com sucesso! "+
                        "É necessário refazer o login para atualizar os dados.", new DialogHelper() {
                    @Override
                    public void onCancel() {

                    }

                    @Override
                    public void onOk() {
                        activity.doLogout();
                    }
                });
            }

            @Override
            public void onResponse(Object data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(Object data, int total) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                activity.errDialog("Erro", msg);
                Log.d(TAG, "erro: "+msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                activity.errDialog("Erro", "Não consegui realizar a operação. Por favor, tente mais tarde");
                Log.d(TAG, "falha");
            }
        };

        if(isCreate)
        {
            api.create(membro, r);
        }
        else
        {
            api.edit(membro, r);
        }
    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onMembresiaFragmentInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnMembresiaFraInteractionListener) {
            mListener = (OnMembresiaFraInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnMembresiaFraInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnMembresiaFraInteractionListener {
        void onMembresiaFragmentInteraction();
    }
}
