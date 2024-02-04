package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.os.Bundle;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.nibblelab.smartchurch.API.TransmissaoAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.TransmissaoAdapter;
import com.nibblelab.smartchurch.model.TransmissaoData;

import java.util.List;

public class TransmissaoFragment extends BaseFragment {

    private OnTransmissaoFragInteractionListener mListener;

    private static final String TAG = "TransmissaoFragment";

    // id's
    String igreja;

    // lista de transmissoes
    RecyclerView transmissoesView;
    RecyclerView.LayoutManager transmissoesLayoutManager;
    TransmissaoAdapter transmissaoAdapter;
    List<TransmissaoData> transmissoes;

    public TransmissaoFragment() {

    }

    public static TransmissaoFragment newInstance() {
        TransmissaoFragment fragment = new TransmissaoFragment();
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
        View inf = inflater.inflate(R.layout.fragment_transmissao, container, false);

        activity.setToolbarTitle(R.string.menu_transmissao);

        igreja = activity.getUser().getMembresia().getIgreja();

        // views
        transmissoesView = (RecyclerView) inf.findViewById(R.id.transmissoes_list);

        this.getTransmissoesDaIgreja();

        return inf;
    }

    public void getTransmissoesDaIgreja()
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
                TransmissaoFragment.this.generateTransmissoesDaIgreja(data);
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

    public void generateTransmissoesDaIgreja(List<TransmissaoData> data)
    {
        transmissoes = data;
        transmissoesLayoutManager = new LinearLayoutManager(this.getContext());
        transmissoesView.setLayoutManager(transmissoesLayoutManager);

        transmissaoAdapter = new TransmissaoAdapter(this.getContext(), transmissoes,this.getLifecycle());
        transmissoesView.setAdapter(transmissaoAdapter);
    }

    public void onButtonPressed() {
        if (mListener != null) {
            mListener.onTransmissaoFragInteraction();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof OnTransmissaoFragInteractionListener) {
            mListener = (OnTransmissaoFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnTransmissaoFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    public interface OnTransmissaoFragInteractionListener {
        void onTransmissaoFragInteraction();
    }
}
