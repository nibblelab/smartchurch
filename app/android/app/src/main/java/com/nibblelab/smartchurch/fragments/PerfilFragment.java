package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.os.Bundle;
import com.google.android.material.textfield.TextInputEditText;
import androidx.appcompat.widget.AppCompatSpinner;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.constraintlayout.widget.ConstraintSet;
import androidx.core.content.res.ResourcesCompat;

import android.util.Log;
import android.util.TypedValue;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioButton;

import com.nibblelab.smartchurch.API.DataListAPI;
import com.nibblelab.smartchurch.API.DoacaoAPI;
import com.nibblelab.smartchurch.API.IbgeAPI;
import com.nibblelab.smartchurch.API.NecessidadeEspecialAPI;
import com.nibblelab.smartchurch.API.PessoaAPI;
import com.nibblelab.smartchurch.API.ProfissaoAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.DateHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.CheckBoxData;
import com.nibblelab.smartchurch.model.DataItemData;
import com.nibblelab.smartchurch.model.DataListData;
import com.nibblelab.smartchurch.model.DoacaoData;
import com.nibblelab.smartchurch.model.NecessidadeEspecialData;
import com.nibblelab.smartchurch.model.PessoaData;
import com.nibblelab.smartchurch.model.ProfissaoData;
import com.nibblelab.smartchurch.model.UFData;
import com.nibblelab.smartchurch.ui.Masks;

import java.util.ArrayList;
import java.util.List;

public class PerfilFragment extends BaseFragment {

    private OnPerfilFragInteractionListener mListener;

    private static final String TAG = "PerfilFragment";

    EditText nome;
    EditText email;
    TextInputEditText senha;
    RadioButton sexo_feminino;
    RadioButton sexo_masculino;
    EditText dtnasc;
    AppCompatSpinner ecivil;
    CheckBox filhos;
    AppCompatSpinner escolaridade;
    AppCompatSpinner profissao;
    EditText cep;
    EditText endereco;
    EditText numero;
    EditText complemento;
    EditText bairro;
    EditText cidade;
    AppCompatSpinner uf;
    EditText telefone;
    EditText celular_1;
    EditText celular_2;

    // dados
    List<DataItemData> estados_civis;
    List<UFData> ufs;
    List<DataItemData> graus_escolaridade;
    List<ProfissaoData> profissoes;
    List<DoacaoData> doacoes;
    List<NecessidadeEspecialData> necessidades;

    // adapters (spinners)
    ArrayAdapter<String> eCivilDataAdapter;
    ArrayAdapter<String> grausDataAdapter;
    ArrayAdapter<String> ufDataAdapter;
    ArrayAdapter<String> profissaoAdapter;

    // botão
    Button save;

    // dados
    PessoaData perfil;

    // doações
    ConstraintLayout doacoesArea;
    ArrayList<CheckBox> doacoesChecks;

    // necessidades
    ConstraintLayout necessidadesArea;
    ArrayList<CheckBox> necessidadesChecks;

    public PerfilFragment() {
        // Required empty public constructor
    }

    public static PerfilFragment newInstance() {
        PerfilFragment fragment = new PerfilFragment();
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

    /**
     * Aplique as máscaras nos campos
     */
    public void applyMasks()
    {
        cep.addTextChangedListener(new Masks("##.###-###"));
        telefone.addTextChangedListener(new Masks("(##) ####-####"));
        celular_1.addTextChangedListener(new Masks("(##) # ####-####"));
        celular_2.addTextChangedListener(new Masks("(##) # ####-####"));
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View inf = inflater.inflate(R.layout.fragment_perfil, container, false);

        activity.setToolbarTitle(R.string.menu_perfil);

        // inputs
        nome = (EditText) inf.findViewById(R.id.p_nome_fld);
        email = (EditText) inf.findViewById(R.id.p_email_fld);
        senha = (TextInputEditText) inf.findViewById(R.id.p_senha_fld);

        sexo_feminino = (RadioButton) inf.findViewById(R.id.p_sexo_fld_feminino_fld);
        sexo_masculino = (RadioButton) inf.findViewById(R.id.p_sexo_fld_masculino_fld);
        dtnasc = (EditText) inf.findViewById(R.id.p_dtnasc_fld);
        ecivil = (AppCompatSpinner) inf.findViewById(R.id.p_ecivil_fld);
        filhos = (CheckBox) inf.findViewById(R.id.p_filhos_fld);
        escolaridade = (AppCompatSpinner) inf.findViewById(R.id.p_escolaridade_fld);
        profissao = (AppCompatSpinner) inf.findViewById(R.id.p_profissao_fld);

        cep = (EditText) inf.findViewById(R.id.p_cep_fld);
        endereco = (EditText) inf.findViewById(R.id.p_endereco_fld);
        numero = (EditText) inf.findViewById(R.id.p_numero_fld);
        complemento = (EditText) inf.findViewById(R.id.p_complemento_fld);
        bairro = (EditText) inf.findViewById(R.id.p_bairro_fld);
        cidade = (EditText) inf.findViewById(R.id.p_cidade_fld);
        uf = (AppCompatSpinner) inf.findViewById(R.id.p_uf_fld);

        telefone = (EditText) inf.findViewById(R.id.p_telefone_fld);
        celular_1 = (EditText) inf.findViewById(R.id.p_celular_1_fld);
        celular_2 = (EditText) inf.findViewById(R.id.p_celular_2_fld);

        doacoesArea = (ConstraintLayout) inf.findViewById(R.id.p_doacoes_area);
        doacoesChecks = new ArrayList<>();

        necessidadesArea = (ConstraintLayout) inf.findViewById(R.id.p_necessidades_area);
        necessidadesChecks = new ArrayList<>();

        this.getDataList();

        save = (Button) inf.findViewById(R.id.perf_save);
        save.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                PerfilFragment.this.savePerfil();
            }
        });

        perfil = new PessoaData<String>();
        perfil.setId(activity.getUser().getId());

        return inf;
    }

    /**
     * Obtenha os dados necessários para o form
     */
    public void getDataList()
    {
        activity.showLoadingSpinner();
        DataListAPI api = new DataListAPI(activity);
        api.getDataLists(new ApiResponse<DataListData>() {
            @Override
            public void onResponse() {

            }

            @Override
            public void onResponse(DataListData data) {
                PerfilFragment.this.generateECivilSpinner(data);
                PerfilFragment.this.generateEscolaridadeSpinner(data);
                PerfilFragment.this.getUFs();
            }

            @Override
            public void onResponse(DataListData data, int total) { }

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
     * Gere o selector de estado civil
     *
     * @param data
     */
    public void generateECivilSpinner(DataListData data)
    {
        estados_civis = data.getEstado_civil();
        List<String> ecivis = new ArrayList<String>();

        for(DataItemData i : estados_civis)
        {
            ecivis.add(i.getLabel());
        }

        eCivilDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, ecivis);
        eCivilDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        ecivil.setAdapter(eCivilDataAdapter);
        ecivil.setSelection(0);

        ecivil.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    /**
     * Gera o seletor de escolaridade
     *
     * @param data
     */
    public void generateEscolaridadeSpinner(DataListData data)
    {
        graus_escolaridade = data.getEscolaridade();
        List<String> graus = new ArrayList<String>();

        for(DataItemData i : graus_escolaridade)
        {
            graus.add(i.getLabel());
        }

        grausDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, graus);
        grausDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        escolaridade.setAdapter(grausDataAdapter);
        escolaridade.setSelection(0);

        escolaridade.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    /**
     * Busque as UFs
     */
    public void getUFs()
    {
        IbgeAPI api = new IbgeAPI(activity);
        api.estados(new ApiResponse<List<UFData>>() {
            @Override
            public void onResponse() {
            }

            @Override
            public void onResponse(List<UFData> data) {
                PerfilFragment.this.generateUFSpinner(data);
                PerfilFragment.this.getProfissoes();
            }

            @Override
            public void onResponse(List<UFData> data, int total) {
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
     * Gera o seletor de UF
     *
     * @param data
     */
    public void generateUFSpinner(List<UFData> data)
    {
        ufs = data;
        List<String> estados = new ArrayList<String>();

        estados.add("");
        for(UFData i : ufs)
        {
            estados.add(i.getNome());
        }

        java.util.Collections.sort(estados);

        ufDataAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, estados);
        ufDataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        uf.setAdapter(ufDataAdapter);
        uf.setSelection(0);

        uf.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    /**
     * Busque as profissões
     */
    public void getProfissoes()
    {
        ProfissaoAPI api = new ProfissaoAPI(activity);
        api.getProfissoes(new ApiResponse<List<ProfissaoData>>() {

            @Override
            public void onResponse() {

            }

            @Override
            public void onResponse(List<ProfissaoData> data) {
                PerfilFragment.this.generateProfissaoSpinner(data);
                PerfilFragment.this.getDoacoes();
            }

            @Override
            public void onResponse(List<ProfissaoData> data, int total) {

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
     * Gere o seletor de profissão
     *
     * @param data
     */
    public void generateProfissaoSpinner(List<ProfissaoData> data)
    {
        profissoes = data;
        List<String> profs = new ArrayList<String>();

        profs.add("");
        for(ProfissaoData i : profissoes)
        {
            profs.add(i.getNome());
        }

        profissaoAdapter = new ArrayAdapter<String>(getActivity(),
                android.R.layout.simple_spinner_item, profs);
        profissaoAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        profissao.setAdapter(profissaoAdapter);
        profissao.setSelection(0);

        profissao.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    /**
     * Busque as doações
     *
     */
    public void getDoacoes()
    {
        DoacaoAPI api = new DoacaoAPI(activity);
        api.getDoacoes(new ApiResponse<List<DoacaoData>>() {

            @Override
            public void onResponse() {

            }

            @Override
            public void onResponse(List<DoacaoData> data) {
                PerfilFragment.this.generateDoacoes(data);
                PerfilFragment.this.getNecessidades();
            }

            @Override
            public void onResponse(List<DoacaoData> data, int total) {

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
     * Gere o view de doações
     *
     * @param data
     */
    public void generateDoacoes(List<DoacaoData> data)
    {
        doacoes = data;
        for(DoacaoData d : doacoes)
        {
            PerfilFragment.this.generateDoacaoView(d.getNome());
        }

        PerfilFragment.this.renderDoacoes();
    }

    /**
     * Gera o view de doação
     *
     * @param doacao
     */
    public void generateDoacaoView(String doacao)
    {
        CheckBox box = new CheckBox(this.getContext());
        box.setId(View.generateViewId());
        box.setText(doacao);
        box.setTypeface(ResourcesCompat.getFont(this.getContext(), R.font.opensans));

        doacoesChecks.add(box);
        doacoesArea.addView(box);
    }

    /**
     * renderiza os views de doação no layout
     */
    public void renderDoacoes()
    {
        ConstraintSet set = new ConstraintSet();
        set.clone(doacoesArea);
        int indx = -1;
        int topMargin = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,8, getResources().getDisplayMetrics());
        int leftMargin = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,32, getResources().getDisplayMetrics());

        for(CheckBox box : doacoesChecks)
        {
            if(indx == -1)
            {
                set.connect(box.getId(), ConstraintSet.TOP, R.id.p_doador_label, ConstraintSet.BOTTOM, topMargin);
                set.connect(box.getId(), ConstraintSet.LEFT, ConstraintSet.PARENT_ID, ConstraintSet.LEFT, leftMargin);
            }
            else
            {
                CheckBox last = doacoesChecks.get(indx);
                set.connect(box.getId(), ConstraintSet.TOP, last.getId(), ConstraintSet.BOTTOM, topMargin);
                set.connect(box.getId(), ConstraintSet.LEFT, ConstraintSet.PARENT_ID, ConstraintSet.LEFT, leftMargin);
            }
            indx++;
        }
        set.applyTo(doacoesArea);
    }

    /**
     * Busque as necessidades especiais
     *
     */
    public void getNecessidades()
    {
        NecessidadeEspecialAPI api = new NecessidadeEspecialAPI(activity);
        api.getNecessidadesEspeciais(new ApiResponse<List<NecessidadeEspecialData>>() {

            @Override
            public void onResponse() {

            }

            @Override
            public void onResponse(List<NecessidadeEspecialData> data) {
                PerfilFragment.this.generateNecessidades(data);
                PerfilFragment.this.getPerfil();
            }

            @Override
            public void onResponse(List<NecessidadeEspecialData> data, int total) {

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
     * Gera o view de necessidades
     *
     * @param data
     */
    public void generateNecessidades(List<NecessidadeEspecialData> data)
    {
        necessidades = data;
        for(NecessidadeEspecialData d : necessidades)
        {
            PerfilFragment.this.generateNecessidadeView(d.getNome());
        }

        PerfilFragment.this.renderNecessidades();
    }

    /**
     * Gere o view de necessidade
     *
     * @param necessidade
     */
    public void generateNecessidadeView(String necessidade)
    {
        CheckBox box = new CheckBox(this.getContext());
        box.setId(View.generateViewId());
        box.setText(necessidade);
        box.setTypeface(ResourcesCompat.getFont(this.getContext(), R.font.opensans));

        necessidadesChecks.add(box);
        necessidadesArea.addView(box);
    }

    /**
     * Renderiza os views de necessidade no layout
     */
    public void renderNecessidades()
    {
        ConstraintSet set = new ConstraintSet();
        set.clone(necessidadesArea);
        int indx = -1;
        int topMargin = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,8, getResources().getDisplayMetrics());
        int leftMargin = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,32, getResources().getDisplayMetrics());

        for(CheckBox box : necessidadesChecks)
        {
            if(indx == -1)
            {
                set.connect(box.getId(), ConstraintSet.TOP, R.id.p_necessidade_label, ConstraintSet.BOTTOM, topMargin);
                set.connect(box.getId(), ConstraintSet.LEFT, ConstraintSet.PARENT_ID, ConstraintSet.LEFT, leftMargin);
            }
            else
            {
                CheckBox last = necessidadesChecks.get(indx);
                set.connect(box.getId(), ConstraintSet.TOP, last.getId(), ConstraintSet.BOTTOM, topMargin);
                set.connect(box.getId(), ConstraintSet.LEFT, ConstraintSet.PARENT_ID, ConstraintSet.LEFT, leftMargin);
            }
            indx++;
        }
        set.applyTo(necessidadesArea);
    }

    /**
     * Busque os dados do perfil
     */
    public void getPerfil()
    {
        PessoaAPI api = new PessoaAPI(activity);
        api.getMe(perfil.getId(), new ApiResponse<PessoaData<String>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(PessoaData<String> data) {
                activity.hideLoadingSpinner();
                PerfilFragment.this.loadPerfil(data);
            }

            @Override
            public void onResponse(PessoaData<String> data, int total) {
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
     * Obtem o rótulo (para o spinner) de um elemento numa lista de dados a partir de seu valor
     *
     * @param l
     * @param value
     * @return
     */
    private String getLabelFromValue(List<DataItemData> l, String value)
    {
        for(DataItemData d : l)
        {
            if(d.getValue().equals(value)) {
                return d.getLabel();
            }
        }

        return "";
    }

    /**
     * Verifica as doações previamente existentes no perfil
     */
    private void checkDoacoesDoPerfil()
    {
        List<String> doacoesPerfil = perfil.getDoacoes();
        for(String d : doacoesPerfil)
        {
            String nome = DoacaoData.getNomeByIdInList(doacoes, d);
            for(CheckBox c : doacoesChecks)
            {
                if(c.getText().equals(nome))
                {
                    c.setChecked(true);
                }
            }
        }
    }

    /**
     * Verifica as necessidades especiais previamente existentes no perfil
     */
    private void checkNecessidadesDoPerfil()
    {
        List<String> necessidadesPerfil = perfil.getNecessidades_especiais();
        for(String n : necessidadesPerfil)
        {
            String nome = NecessidadeEspecialData.getNomeByIdInList(necessidades, n);
            for(CheckBox c : necessidadesChecks)
            {
                if(c.getText().equals(nome))
                {
                    c.setChecked(true);
                }
            }
        }
    }

    /**
     * Carregue os dados do perfil
     *
     * @param data
     */
    public void loadPerfil(PessoaData<String> data)
    {
        perfil.load(data);

        nome.setText(perfil.getNome());

        email.setText(perfil.getEmail());

        String sexo = perfil.getSexo();
        if(sexo.equals("F")) {
            sexo_feminino.setChecked(true);
        }
        else if(sexo.equals("M")) {
            sexo_masculino.setChecked(true);
        }

        dtnasc.setText(DateHelper.fromDBDateToHumanDate(perfil.getData_nascimento()));

        ecivil.setSelection(eCivilDataAdapter.getPosition(this.getLabelFromValue(this.estados_civis, perfil.getEstado_civil())));

        filhos.setChecked(perfil.getTem_filhos());

        escolaridade.setSelection(grausDataAdapter.getPosition(this.getLabelFromValue(this.graus_escolaridade, perfil.getEscolaridade())));

        profissao.setSelection(profissaoAdapter.getPosition(ProfissaoData.getNomeById(profissoes, perfil.getProfissao())));

        cep.setText(perfil.getCep());
        endereco.setText(perfil.getEndereco());
        numero.setText(perfil.getNumero());
        complemento.setText(perfil.getComplemento());
        bairro.setText(perfil.getBairro());
        cidade.setText(perfil.getCidade());

        uf.setSelection(ufDataAdapter.getPosition(UFData.getNomeBySigla(ufs, perfil.getUf())));

        telefone.setText(perfil.getTelefone());
        celular_1.setText(perfil.getCelular_1());
        celular_2.setText(perfil.getCelular_2());

        this.checkDoacoesDoPerfil();
        this.checkNecessidadesDoPerfil();

        this.applyMasks();

    }

    /**
     * Obtem o valor de um elemento numa lista de dados a partir de seu rótulo (usado no spinner)
     *
     * @param l
     * @param label
     * @return
     */
    private String getValueFromLabel(List<DataItemData> l, String label)
    {
        for(DataItemData d : l)
        {
            if(d.getLabel().equals(label)) {
                return d.getValue();
            }
        }

        return "";
    }

    /**
     * Obtêm a sigla de um UF pelo seu nome
     *
     * @param nome
     * @return
     */
    private String getSiglaUFByNome(String nome)
    {
        for(UFData d : ufs)
        {
            if(d.getNome().equals(nome)) {
                return d.getSigla();
            }
        }

        return "";
    }

    /**
     * Gera a lista de doações conforme o view
     *
     * @return
     */
    private List<CheckBoxData> generateDoacoesList()
    {
        List<CheckBoxData> data = new ArrayList<>();

        for(CheckBox c : doacoesChecks)
        {
            String id = DoacaoData.getIdByNomeInList(doacoes, c.getText().toString());
            if(StringHelper.notEmpty(id)) {
                data.add(new CheckBoxData(c.isChecked(), id));
            }
        }

        return data;
    }

    /**
     * Gera a lista de necessidades conforme o view
     *
     * @return
     */
    private List<CheckBoxData> generateNecessidadesList()
    {
        List<CheckBoxData> data = new ArrayList<>();

        for(CheckBox c : necessidadesChecks)
        {
            String id = NecessidadeEspecialData.getIdByNomeInList(necessidades, c.getText().toString());
            if(StringHelper.notEmpty(id)) {
                data.add(new CheckBoxData(c.isChecked(), id));
            }
        }

        return data;
    }

    /**
     * Salve as alterações nos dados do usuário (perfil)
     */
    public void savePerfil()
    {
        PessoaData<CheckBoxData> pessoa = new PessoaData<>();

        pessoa.setId(perfil.getId());
        pessoa.setNome(nome.getText().toString());
        pessoa.setEmail(email.getText().toString());
        pessoa.setSenha(senha.getText().toString());

        String sexo = "";
        if(sexo_feminino.isChecked()) {
            sexo = "F";
        }
        else if(sexo_masculino.isChecked()) {
            sexo = "M";
        }
        pessoa.setSexo(sexo);

        pessoa.setData_nascimento(DateHelper.fromHumamDateToDBDate(dtnasc.getText().toString()));

        pessoa.setEstado_civil(this.getValueFromLabel(this.estados_civis, ecivil.getSelectedItem().toString()));

        pessoa.setTem_filhos(filhos.isChecked());

        pessoa.setEscolaridade(this.getValueFromLabel(this.graus_escolaridade, escolaridade.getSelectedItem().toString()));

        pessoa.setProfissao(ProfissaoData.getIdByNome(profissoes, profissao.getSelectedItem().toString()));

        pessoa.setCep(cep.getText().toString());

        pessoa.setEndereco(endereco.getText().toString());

        pessoa.setNumero(numero.getText().toString());

        pessoa.setComplemento(complemento.getText().toString());

        pessoa.setBairro(bairro.getText().toString());

        pessoa.setCidade(cidade.getText().toString());

        pessoa.setUf(this.getSiglaUFByNome(uf.getSelectedItem().toString()));

        pessoa.setTelefone(telefone.getText().toString());
        pessoa.setCelular_1(celular_1.getText().toString());
        pessoa.setCelular_2(celular_2.getText().toString());

        pessoa.setDoacoes(this.generateDoacoesList());
        pessoa.setNecessidades_especiais(this.generateNecessidadesList());

        activity.showLoadingSpinner();
        PessoaAPI api = new PessoaAPI(activity);
        api.save(pessoa, new ApiResponse<Object>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
                activity.successDialog("Sucesso", "Seus dados foram salvos com sucesso!");
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
        });

    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onPerfilFragInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnPerfilFragInteractionListener) {
            mListener = (OnPerfilFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnPerfilFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnPerfilFragInteractionListener {
        void onPerfilFragInteraction();
    }
}
