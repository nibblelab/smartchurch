package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.TemploRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TemploData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TemploAPI extends BaseAPI {

    public final String TAG = "TemploAPI";

    public TemploAPI(final Base b) {
        super(b);
    }

    public void getTemplos(String searchBy, String stat, String sinodo, String presbiterio, String igreja, final ApiResponse r)
    {
        Call<ResponseData<List<TemploData>>> call = new TemploRequests().getService().getAll(searchBy, stat, sinodo, presbiterio, igreja, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<TemploData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<TemploData>>> call, Response<ResponseData<List<TemploData>>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse(data.getDatas());
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<List<TemploData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getTemplos] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getTemplos(final ApiResponse r)
    {
        this.getTemplos("","","","","", r);
    }

    public void getTemplosAtivos(String nome, String sinodo, String presbiterio, String igreja, final ApiResponse r)
    {
        this.getTemplos(nome,Status.ATIVO,sinodo,presbiterio,igreja, r);
    }

    public void getTemplosAtivos(final ApiResponse r)
    {
        this.getTemplos("",Status.ATIVO, "", "","", r);
    }

    public void getTemplosDoSinodo(String sinodo, final ApiResponse r)
    {
        this.getTemplos("", "", sinodo, "","", r);
    }

    public void getTemplosDoPresbiterio(String presbiterio, final ApiResponse r)
    {
        this.getTemplos("", "", "", presbiterio,"", r);
    }

    public void getTemplosDoPresbiterioESinodo(String sinodo, String presbiterio, final ApiResponse r)
    {
        this.getTemplos("", "", sinodo, presbiterio,"", r);
    }

    public void getTemplosAtivosDoSinodo(String sinodo, final ApiResponse r)
    {
        this.getTemplos("", Status.ATIVO, sinodo, "","", r);
    }

    public void getTemplosAtivosDoPresbiterio(String presbiterio, final ApiResponse r)
    {
        this.getTemplos("", Status.ATIVO, "", presbiterio,"", r);
    }

    public void getTemplosAtivosDoPresbiterioESinodo(String sinodo, String presbiterio, final ApiResponse r)
    {
        this.getTemplos("", Status.ATIVO, sinodo, presbiterio,"", r);
    }
}
