package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.OficialRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.OficialData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class OficialAPI extends BaseAPI {
    public final String TAG = "OficialAPI";

    public OficialAPI(final Base b) {
        super(b);
    }

    public void getOficiais(String page, String pagesize, String searchBy, String orderBy, String stat, String cargo,
                                String diretoria, String pessoa, String igreja, String sociedade, String sexo, String estado_civil, String escolaridade, boolean com_filhos,
                                boolean sem_filhos, final ApiResponse r)
    {
        Call<ResponseData<List<OficialData>>> call = new OficialRequests().getService().getAll(page, pagesize, searchBy, orderBy, stat, cargo,
                diretoria, pessoa, igreja, sexo, sociedade, estado_civil, escolaridade, com_filhos,
                sem_filhos, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<OficialData>>>() {
            @Override
            public void onResponse(Call<ResponseData<List<OficialData>>> call, Response<ResponseData<List<OficialData>>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse(data.getDatas(), data.getTotal());
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<List<OficialData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getOficiais] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllOficiais(final ApiResponse r)
    {
        getOficiais("", "", "", "", "", "",
                "", "", "", "", "", "", "", false,
                false, r);
    }

    public void getAllOficiaisForIgreja(String igreja, final ApiResponse r)
    {
        getOficiais("", "", "", "", "", "",
                "", "", igreja, "", "", "", "", false,
                false, r);
    }
}
