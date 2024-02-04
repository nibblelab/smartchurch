package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;


import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.kizitonwose.calendarview.CalendarView;
import com.kizitonwose.calendarview.model.CalendarDay;
import com.kizitonwose.calendarview.model.CalendarMonth;
import com.kizitonwose.calendarview.model.DayOwner;
import com.kizitonwose.calendarview.ui.DayBinder;
import com.kizitonwose.calendarview.ui.MonthHeaderFooterBinder;
import com.nibblelab.smartchurch.API.AgendaAPI;
import com.nibblelab.smartchurch.API.CargoAPI;
import com.nibblelab.smartchurch.API.DiaconoAPI;
import com.nibblelab.smartchurch.API.EvangelistaAPI;
import com.nibblelab.smartchurch.API.GenericAPI;
import com.nibblelab.smartchurch.API.MinisterioAPI;
import com.nibblelab.smartchurch.API.OficialAPI;
import com.nibblelab.smartchurch.API.PastorAPI;
import com.nibblelab.smartchurch.API.PresbiteroAPI;
import com.nibblelab.smartchurch.API.SecretariaAPI;
import com.nibblelab.smartchurch.API.SociedadeAPI;
import com.nibblelab.smartchurch.API.TagAgendaAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.AgendaAdapter;
import com.nibblelab.smartchurch.calendar.DayViewContainer;
import com.nibblelab.smartchurch.calendar.MonthHeaderViewContainer;
import com.nibblelab.smartchurch.common.DateHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.AgendaData;
import com.nibblelab.smartchurch.model.AgendaResponsavelData;
import com.nibblelab.smartchurch.model.CargoData;
import com.nibblelab.smartchurch.model.MinisterioData;
import com.nibblelab.smartchurch.model.OficialData;
import com.nibblelab.smartchurch.model.SecretariaData;
import com.nibblelab.smartchurch.model.SociedadeData;
import com.nibblelab.smartchurch.model.TagAgendaData;

import java.time.DayOfWeek;
import java.time.YearMonth;
import java.time.temporal.WeekFields;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;


public class AgendaFragment extends BaseFragment implements DayViewContainer.OnDayViewContainerListener {

    private OnAgendaFragInteractionListener mListener;

    private static final String TAG = "AgendaFragment";

    // calendário
    CalendarView calendar;

    // lista da agenda
    List<AgendaData> agenda;
    RecyclerView eventosView;
    RecyclerView.LayoutManager eventosLayoutManager;
    AgendaAdapter eventoAdapter;

    // listas de dados da agenda
    List<TagAgendaData> tags;
    List<CargoData> cargos;
    List<OficialData> oficiais;
    List<OficialData> diaconos;
    List<OficialData> presbiteros;
    List<OficialData> evangelistas;
    List<OficialData> pastores;
    List<MinisterioData> ministerios;
    List<SecretariaData> secretarias;
    List<SociedadeData> sociedades;
    List<AgendaResponsavelData> responsaveis;

    // agenda
    TextView diaNome;
    TextView dia;
    TextView mes;
    TextView ano;

    // controles
    RelativeLayout calendarioListWrp;
    RelativeLayout calendarioDataWrp;
    FloatingActionButton calendarioBack;

    // id's
    String igreja;

    public AgendaFragment() {
        // Required empty public constructor
    }

    public static AgendaFragment newInstance() {
        AgendaFragment fragment = new AgendaFragment();
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
        View inf = inflater.inflate(R.layout.fragment_agenda, container, false);

        activity.setToolbarTitle(R.string.menu_agenda);

        igreja = activity.getUser().getMembresia().getIgreja();
        responsaveis = new ArrayList<>();

        // view
        calendarioListWrp = (RelativeLayout) inf.findViewById(R.id.include_calendario);
        calendarioDataWrp = (RelativeLayout) inf.findViewById(R.id.include_dia);
        eventosView = (RecyclerView) inf.findViewById(R.id.eventos_list);

        // calendário
        calendar = (CalendarView) inf.findViewById(R.id.calendarView);

        // agenda
        diaNome = (TextView) inf.findViewById(R.id.calendar_dt_dianome);
        dia = (TextView) inf.findViewById(R.id.calendar_dt_dia);
        mes = (TextView) inf.findViewById(R.id.calendar_dt_mes);
        ano = (TextView) inf.findViewById(R.id.calendar_dt_ano);

        // controles
        calendarioBack = (FloatingActionButton) inf.findViewById(R.id.calendario_back);

        // handler do botão de volta do modo exibição para o modo listagem
        calendarioBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                // pare os possíveis players
                if(eventoAdapter != null) {
                    eventoAdapter.stopPlayers();
                }

                AgendaFragment.this.toggleListArea(true);
            }
        });

        this.generateCalendar();
        this.toggleListArea(true);

        this.getTagsAgenda();

        return inf;
    }

    /*******************************************************
     *      MÉTODOS DE CONTROLE
     *******************************************************/

    /**
     * Troque o view de listagem pelo de dados
     *
     * @param show
     */
    public void toggleListArea(boolean show)
    {
        if(show) {
            calendarioListWrp.setVisibility(View.VISIBLE);
            calendarioDataWrp.setVisibility(View.GONE);
        }
        else {
            calendarioListWrp.setVisibility(View.GONE);
            calendarioDataWrp.setVisibility(View.VISIBLE);
        }
    }

    public void toCalendarioList()
    {
        this.toggleListArea(true);
    }

    /*******************************************************
     *      MÉTODOS RELATIVOS A BUSCA DE AGENDA
     *******************************************************/

    /**
     * Retorno genérico para erros na API
     *
     * @param msg
     */
    public void showAPIError(String msg) {
        activity.hideLoadingSpinner();
        Log.d(TAG, "Erro :" + msg);
    }

    public void getTagsAgenda()
    {
        activity.showLoadingSpinner();
        TagAgendaAPI api = new TagAgendaAPI(activity);
        GenericAPI<List<TagAgendaData>> genApi = new GenericAPI<>();
        genApi.getAll(api, "getAllTagsAgenda", data -> AgendaFragment.this.prepareTagsAgenda(data), error -> AgendaFragment.this.showAPIError("[getTagsAgenda] " + error));
    }

    public void prepareTagsAgenda(List<TagAgendaData> data)
    {
        tags = data;
        for(TagAgendaData t : tags)
        {
            t.generateColor();
        }
        this.getCargos();
    }

    /**
     * Busque os cargos
     */
    public void getCargos()
    {
        CargoAPI api = new CargoAPI(activity);
        GenericAPI<List<CargoData>> genApi = new GenericAPI<>();
        genApi.getAll(api, "getAllCargos", data -> AgendaFragment.this.prepareCargos(data), error -> AgendaFragment.this.showAPIError("[getCargos] " + error));
    }

    public void prepareCargos(List<CargoData> data)
    {
        this.cargos = data;
        this.getDiaconos();
    }

    /**
     * Busque os diáconos
     */
    public void getDiaconos()
    {
        DiaconoAPI api = new DiaconoAPI(activity);
        GenericAPI<List<OficialData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllDiaconosForIgreja", igreja, data -> AgendaFragment.this.prepareDiaconos(data), error -> AgendaFragment.this.showAPIError("[getDiaconos] " + error));
    }

    public void prepareDiaconos(List<OficialData> data) {
        this.diaconos = data;
        for(OficialData d : this.diaconos)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getPresbiteros();
    }

    /**
     * Busque os prebíteros
     */
    public void getPresbiteros()
    {
        PresbiteroAPI api = new PresbiteroAPI(activity);
        GenericAPI<List<OficialData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllPresbiterosForIgreja", igreja, data -> AgendaFragment.this.preparePresbiteros(data), error -> AgendaFragment.this.showAPIError("[getPresbiteros] " + error));
    }

    public void preparePresbiteros(List<OficialData> data)
    {
        this.presbiteros = data;
        for(OficialData d : this.presbiteros)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getEvangelistas();
    }

    /**
     * Busque os evangelistas
     */
    public void getEvangelistas()
    {
        EvangelistaAPI api = new EvangelistaAPI(activity);
        GenericAPI<List<OficialData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllEvangelistasForIgreja", igreja, data -> AgendaFragment.this.prepareEvangelistas(data), error -> AgendaFragment.this.showAPIError("[getEvangelistas] " + error));
    }

    public void prepareEvangelistas(List<OficialData> data)
    {
        this.evangelistas = data;
        for(OficialData d : this.evangelistas)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getPastores();
    }

    /**
     * Busque os pastores
     */
    public void getPastores()
    {
        PastorAPI api = new PastorAPI(activity);
        GenericAPI<List<OficialData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllPastoresForIgreja", igreja, data -> AgendaFragment.this.preparePastores(data), error -> AgendaFragment.this.showAPIError("[getPastores] " + error));
    }

    public void preparePastores(List<OficialData> data)
    {
        this.pastores = data;
        for(OficialData d : this.pastores)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getOficiais();
    }

    /**
     * Busque os oficiais
     */
    public void getOficiais()
    {
        OficialAPI api = new OficialAPI(activity);
        GenericAPI<List<OficialData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllOficiaisForIgreja", igreja, data -> AgendaFragment.this.prepareOficiais(data), error -> AgendaFragment.this.showAPIError("[getOficiais] " + error));
    }

    public void prepareOficiais(List<OficialData> data)
    {
        this.oficiais = data;
        for(OficialData d : this.oficiais)
        {
            String nome = d.getNome();
            if(StringHelper.notEmpty(d.getCargo()))
            {
                CargoData cargo = cargos
                        .stream()
                        .filter( t -> t.getId().equals(d.getCargo()))
                        .findAny()
                        .orElse(null);
                if(cargo != null) {
                    nome = "[" + cargo.getNome() + "] " + d.getNome();
                }
            }
            responsaveis.add(new AgendaResponsavelData(d.getId(), nome));
        }
        this.getMinisterios();
    }

    /**
     * Busque os ministérios
     */
    public void getMinisterios()
    {
        MinisterioAPI api = new MinisterioAPI(activity);
        GenericAPI<List<MinisterioData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllMinisteriosForIgreja", igreja, data -> AgendaFragment.this.prepareMinisterios(data), error -> AgendaFragment.this.showAPIError("[getMinisterios] " + error));
    }

    public void prepareMinisterios(List<MinisterioData> data)
    {
        this.ministerios = data;
        for(MinisterioData d: this.ministerios)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getSecretarias();
    }

    /**
     * Busque as secretarias
     */
    public void getSecretarias()
    {
        SecretariaAPI api = new SecretariaAPI(activity);
        GenericAPI<List<SecretariaData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllSecretariasForIgreja", igreja, data -> AgendaFragment.this.prepareSecretarias(data), error -> AgendaFragment.this.showAPIError("[getSecretarias] " + error));
    }

    public void prepareSecretarias(List<SecretariaData> data)
    {
        this.secretarias = data;
        for(SecretariaData d: this.secretarias)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getSociedades();
    }

    /**
     * Busque as sociedades internas
     */
    public void getSociedades()
    {
        SociedadeAPI api = new SociedadeAPI(activity);
        GenericAPI<List<SociedadeData>> genApi = new GenericAPI<>();
        genApi.getAllBy(api, "getAllSociedadesForIgreja", igreja, data -> AgendaFragment.this.prepareSociedades(data), error -> AgendaFragment.this.showAPIError("[getSociedades] " + error));
    }

    public void prepareSociedades(List<SociedadeData> data)
    {
        this.sociedades = data;
        for(SociedadeData d: this.sociedades)
        {
            responsaveis.add(new AgendaResponsavelData(d.getId(), d.getNome()));
        }
        this.getAgenda();
    }

    /**
     * Busque a agenda
     */
    public void getAgenda()
    {
        AgendaAPI api = new AgendaAPI(activity);
        api.getAllAgendaForIgreja(igreja, new ApiResponse<List<AgendaData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<AgendaData> data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<AgendaData> data, int total) {
                activity.hideLoadingSpinner();
                AgendaFragment.this.markEventsInCalendar(data, total);
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

    public boolean agendaInDay(AgendaData d, String dia)
    {
        if(!d.isRecorrente() && DateHelper.isBetweenDates(dia, DateHelper.getHumanDateFromDBDateTime(d.getTime_ini()), DateHelper.getHumanDateFromDBDateTime(d.getTime_end())))
        {
            return true;
        }
        else if(d.isRecorrente())
        {
            if(d.checkIfDateHasRecorrente(dia))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o dia tem evento
     *
     * @param dia
     * @return
     */
    public boolean dayHasEvent(String dia)
    {
        if(agenda == null) return false;
        for(AgendaData d : agenda)
        {
            if(this.agendaInDay(d, dia))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Gere o calendário
     */
    public void generateCalendar()
    {
        calendar.setDayBinder(new DayBinder<DayViewContainer>() {
            @Override
            public DayViewContainer create(View view) {
                return new DayViewContainer(view, AgendaFragment.this);
            }

            @Override
            public void bind(DayViewContainer dayViewContainer, CalendarDay calendarDay) {
                dayViewContainer.day.setText(Integer.toString(calendarDay.getDate().getDayOfMonth()));
                if (calendarDay.getOwner() == DayOwner.THIS_MONTH) {
                    if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                        dayViewContainer.day.setTextColor(getContext().getColor(R.color.validDayColor));
                    }
                    else {
                        dayViewContainer.day.setTextColor(Color.parseColor("#515151"));
                    }
                } else {
                    dayViewContainer.day.setTextColor(Color.GRAY);
                    if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                        dayViewContainer.day.setTextColor(getContext().getColor(R.color.invalidDayColor));
                    }
                    else {
                        dayViewContainer.day.setTextColor(Color.parseColor("#e4e4e4"));
                    }
                }

                dayViewContainer.calendarDay = calendarDay;

                String dia = DateHelper.getHumanDateFromInts(calendarDay.getDate().getDayOfMonth(),
                        calendarDay.getDate().getMonthValue(),
                        calendarDay.getDate().getYear());
                if(AgendaFragment.this.dayHasEvent(dia))
                {
                    dayViewContainer.marker.setVisibility(View.VISIBLE);
                }
            }
        });

        calendar.setMonthHeaderBinder(new MonthHeaderFooterBinder<MonthHeaderViewContainer>() {
            @Override
            public MonthHeaderViewContainer create(View view) {
                return new MonthHeaderViewContainer(view);
            }

            @Override
            public void bind(MonthHeaderViewContainer monthViewContainer, CalendarMonth calendarMonth) {
                int m = calendarMonth.getMonth();
                String mes = DateHelper.getMonthName(m);
                int ano = calendarMonth.getYear();
                mes += " " + DateHelper.getYear(ano);
                monthViewContainer.month.setText(mes);
            }
        });

        YearMonth currentMonth = YearMonth.now();
        YearMonth firstMonth = currentMonth.minusMonths(10);
        YearMonth lastMonth = currentMonth.plusMonths(10);
        DayOfWeek firstDayOfWeek = WeekFields.of(Locale.getDefault()).getFirstDayOfWeek();
        calendar.setup(firstMonth, lastMonth, firstDayOfWeek);
        calendar.scrollToMonth(currentMonth);
    }

    /**
     * Marque os eventos da agenda no calendário
     *
     * @param data
     * @param total
     */
    public void markEventsInCalendar(List<AgendaData> data, int total)
    {
        if(total > 0)
        {
            agenda = data;
            calendar.notifyCalendarChanged();
        }
    }

    /*******************************************************
     *      MÉTODOS RELATIVOS A EXIBIÇÃO DE DIA
     *******************************************************/

    /**
     * listenner do clique em dia na agenda
     *
     * @param dia
     * @param mes
     * @param ano
     */
    @Override
    public void onDayViewContainerClick(int dia, int mes, int ano) {
        String data = DateHelper.getHumanDateFromInts(dia, mes, ano);
        List<AgendaData> agenda = getEventosDoDia(data);
        if(agenda != null && agenda.size() > 0) {
            showAgenda(agenda, data);
            this.toggleListArea(false);
        }
        else {
            activity.infoDialog(getResources().getString(R.string.no_data), getResources().getString(R.string.cal_no_data));
        }
    }

    public List<AgendaData> getEventosDoDia(String data)
    {
        List<AgendaData> r = new ArrayList<>();

        for(AgendaData d : agenda)
        {
            if(this.agendaInDay(d, data))
            {
                r.add(d);
            }
        }

        return r;
    }

    public void showAgenda(List<AgendaData> d, String data)
    {
        diaNome.setText(DateHelper.getDayNameFromHumanDate(data));
        dia.setText(DateHelper.getDayFromHumanDate(data));
        mes.setText(DateHelper.getMonthNameFromHumanDate(data));
        ano.setText(DateHelper.getYearFromHumanDate(data));

        // itere a lista de eventos
        eventosLayoutManager = new LinearLayoutManager(this.getContext());
        eventosView.setLayoutManager(eventosLayoutManager);

        eventoAdapter = new AgendaAdapter(this.getContext(), data, d, tags, getLifecycle(), responsaveis);
        eventosView.setAdapter(eventoAdapter);
    }


    /*******************************************************
     *      MÉTODOS DO FRAGMENT
     *******************************************************/

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof AgendaFragment.OnAgendaFragInteractionListener) {
            mListener = (AgendaFragment.OnAgendaFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnAgendaFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnAgendaFragInteractionListener {
        void onAgendaFragInteraction();
    }
}