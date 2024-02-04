package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.RelativeLayout;
import android.widget.ScrollView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.github.irshulx.Editor;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.nibblelab.smartchurch.API.PedidoDeOracaoAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.PedidoDeOracaoAdapter;
import com.nibblelab.smartchurch.common.DialogHelper;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.PedidoDeOracaoData;
import com.nibblelab.smartchurch.ui.events.PedidoOracaoListEvents;
import com.nibblelab.smartchurch.ui.states.FragmentStates;

import java.util.List;

public class OracaoFragment extends BaseFragment implements PedidoOracaoListEvents {
    private static final String TAG = "OracaoFragment";
    private OracaoFragment.OnOracaoFragInteractionListener mListener;

    // id's
    String igreja;
    String userId;

    // lista
    ScrollView pedidosScroll;
    RecyclerView pedidosView;
    RecyclerView.LayoutManager pedidosLayoutManager;
    PedidoDeOracaoAdapter pedidoAdapter;
    List<PedidoDeOracaoData> pedidos;

    // formulário
    //EditText pedido_oracao;
    Editor pedido_oracao;
    Button save;
    PedidoDeOracaoData pedido;

    // controles
    RelativeLayout pedidosListWrp;
    RelativeLayout pedidoFormWrp;
    FloatingActionButton pedidoBack;
    FloatingActionButton pedidoListPrev;
    FloatingActionButton pedidoListNext;
    FloatingActionButton pedidoAdd;
    int page = 1;
    int pageSize = 10;
    boolean isCreate = false;
    FragmentStates state;

    public OracaoFragment() {
        // Required empty public constructor
    }

    public static OracaoFragment newInstance() {
        OracaoFragment fragment = new OracaoFragment();
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
                             Bundle savedInstanceState)
    {
        View inf = inflater.inflate(R.layout.fragment_oracao, container, false);

        activity.setToolbarTitle(R.string.menu_pedido_oracao);

        igreja = activity.getUser().getMembresia().getIgreja();
        userId = activity.getUser().getId();

        // views
        pedidosView = (RecyclerView) inf.findViewById(R.id.pedidos_list);
        pedidosListWrp = (RelativeLayout) inf.findViewById(R.id.include_pedidos);
        pedidoFormWrp = (RelativeLayout) inf.findViewById(R.id.include_pedido_form);
        pedidosScroll = (ScrollView) inf.findViewById(R.id.pedidos_scroll);

        // form
        //pedido_oracao = (EditText) inf.findViewById(R.id.po_frm_fld);
        pedido_oracao = (Editor) inf.findViewById(R.id.po_frm_fld);
        save = (Button) inf.findViewById(R.id.po_frm_save);

        // controles
        pedidoListPrev = (FloatingActionButton) inf.findViewById(R.id.pedidos_anterior);
        pedidoListNext = (FloatingActionButton) inf.findViewById(R.id.pedidos_proximo);
        pedidoAdd = (FloatingActionButton) inf.findViewById(R.id.pedido_add);
        pedidoBack = (FloatingActionButton) inf.findViewById(R.id.pedido_back);

        // handlers
        pedidoListPrev.setOnClickListener(v -> toPrevPage());
        pedidoListNext.setOnClickListener(v -> toNextPage());
        pedidoAdd.setOnClickListener(v -> toAdd());
        pedidoBack.setOnClickListener(v -> backToLista());
        save.setOnClickListener(v -> save());

        // configuração da listagem
        pedidoListPrev.hide();
        pedidoListNext.hide();

        pedidosListWrp.setVisibility(View.GONE);
        pedidoFormWrp.setVisibility(View.GONE);

        state = FragmentStates.NEUTRAL;
        getPedidosDeOracao();

        return inf;
    }

    /*****************************************************************************
     *      MÉTODOS RELATIVOS A BUSCA DE PEDIDOS DE ORAÇÃO
     *****************************************************************************/

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
        this.getPedidosDeOracao();
    }

    /**
     * Volte à página anterior
     */
    public void toPrevPage()
    {
        page--;
        this.getPedidosDeOracao();
    }

    public void getPedidosDeOracao()
    {
        activity.showLoadingSpinner();
        PedidoDeOracaoAPI api = new PedidoDeOracaoAPI(activity);
        api.getPedidosDeOracaoDaIgreja(igreja, page, pageSize, new ApiResponse<List<PedidoDeOracaoData>>() {

            @Override
            public void onResponse() { activity.hideLoadingSpinner(); }

            @Override
            public void onResponse(List<PedidoDeOracaoData> data) { activity.hideLoadingSpinner(); }

            @Override
            public void onResponse(List<PedidoDeOracaoData> data, int total) {
                activity.hideLoadingSpinner();
                OracaoFragment.this.generatePedidosDeOracao(data, total);
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
     * Gere a lista com os pedidos de oração buscados
     *
     * @param datas
     * @param total
     */
    public void generatePedidosDeOracao(List<PedidoDeOracaoData> datas, int total) {
        toggleListArea(true);
        if(total > 0) {
            pedidos = datas;
            pedidosLayoutManager = new LinearLayoutManager(this.getContext());
            pedidosView.setLayoutManager(pedidosLayoutManager);

            pedidoAdapter = new PedidoDeOracaoAdapter(this.getContext(), userId, pedidos, this);
            pedidosView.setAdapter(pedidoAdapter);

            // ajuste a altura
            RelativeLayout.LayoutParams lp = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT);
            pedidosView.setLayoutParams(lp);

            // veja se é possível exibir o botão de página anterior
            if(page == 1) {
                pedidoListPrev.hide();
            }
            else {
                pedidoListPrev.show();
            }

            // veja se é possível exibir o botão de próxima página
            if(hasNextPage(total)) {
                pedidoListNext.show();
            }
            else {
                pedidoListNext.hide();
            }

            // vá para o topo da lista
            pedidosScroll.fullScroll(ScrollView.FOCUS_UP);

            // feche o teclado caso ele ainda esteja aberto
            View view = activity.getCurrentFocus();
            if (view != null) {
                InputMethodManager imm = (InputMethodManager) activity.getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
            }
        }
        else {
            activity.infoDialog(":(", "Sem dados para exibir");
        }
    }

    /**
     * Troque o view de listagem pelo formulário ou vice versa
     *
     * @param show
     */
    public void toggleListArea(boolean show)
    {
        if(show) {
            if(state != FragmentStates.LIST) {
                state = FragmentStates.LIST;
                pedidosListWrp.setVisibility(View.VISIBLE);
                pedidoFormWrp.setVisibility(View.GONE);
            }
        }
        else {
            state = FragmentStates.FORM;
            pedidosListWrp.setVisibility(View.GONE);
            pedidoFormWrp.setVisibility(View.VISIBLE);
        }
    }

    /*****************************************************************************
     *      MÉTODOS RELATIVOS AO FORMULÁRIO DE PEDIDO DE ORAÇÃO
     *****************************************************************************/

    public void backToLista()
    {
        getPedidosDeOracao();
    }

    public void clearPedido()
    {
        pedido_oracao.clearAllContents();
    }

    public void toAdd()
    {
        clearPedido();
        isCreate = true;
        toggleListArea(false);
    }

    public void toEdit()
    {
        clearPedido();
        pedido_oracao.render(pedido.getPedido());
        isCreate = false;
        toggleListArea(false);
    }

    public void save()
    {
        if(!StringHelper.notEmpty(pedido_oracao.getContentAsHTML()))
        {
            activity.errDialog("Erro", "Os campos são obrigatórios!");
            return;
        }

        ApiResponse<Object> response = new ApiResponse<Object>(){
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
                clearPedido();
                activity.successDialog("Sucesso", "Seus pedido de oração foi registrado com sucesso!", new DialogHelper() {
                    @Override
                    public void onCancel() {

                    }

                    @Override
                    public void onOk() {
                        backToLista();
                    }
                });
            }

            @Override
            public void onResponse(Object data) { activity.hideLoadingSpinner(); }

            @Override
            public void onResponse(Object data, int total) { activity.hideLoadingSpinner(); }

            @Override
            public void onAlreadyExecuted() {

            }

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

        if(isCreate) {
            pedido = new PedidoDeOracaoData();
            pedido.setIgreja(igreja);
            pedido.setAutor(activity.getUser().getId());
        }

        pedido.setPedido(pedido_oracao.getContentAsHTML());

        activity.showLoadingSpinner();
        PedidoDeOracaoAPI api = new PedidoDeOracaoAPI(activity);
        if(isCreate) {
            api.create(pedido, response);
        }
        else {
            api.edit(pedido, response);
        }
    }

    public void remove()
    {
        activity.showLoadingSpinner();
        PedidoDeOracaoAPI api = new PedidoDeOracaoAPI(activity);
        api.delete(pedido, new ApiResponse<Object>(){
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
                clearPedido();
                activity.successDialog("Sucesso", "Pedido removido com sucesso", new DialogHelper() {
                    @Override
                    public void onCancel() {

                    }

                    @Override
                    public void onOk() {
                        getPedidosDeOracao();
                    }
                });
            }

            @Override
            public void onResponse(Object data) { activity.hideLoadingSpinner(); }

            @Override
            public void onResponse(Object data, int total) { activity.hideLoadingSpinner(); }

            @Override
            public void onAlreadyExecuted() {

            }

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

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OracaoFragment.OnOracaoFragInteractionListener) {
            mListener = (OracaoFragment.OnOracaoFragInteractionListener) context;
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

    @Override
    public void onEditPedidoOracao(Object d) {
        pedido = (PedidoDeOracaoData) d;
        toEdit();
    }

    @Override
    public void onDeletePedidoOracao(Object d) {
        activity.confirmDialog("Aviso", "Tem certeza que deseja remover?", new DialogHelper() {

            @Override
            public void onCancel() {

            }

            @Override
            public void onOk() {
                pedido = (PedidoDeOracaoData) d;
                remove();
            }
        });
    }

    public interface OnOracaoFragInteractionListener {
        void onOracaoFragmentInteraction();
    }
}
