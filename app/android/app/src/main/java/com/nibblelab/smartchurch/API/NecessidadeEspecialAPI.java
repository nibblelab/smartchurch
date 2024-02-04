package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.NecessidadeEspecialRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.NecessidadeEspecialData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NecessidadeEspecialAPI extends BaseAPI {

    public final String TAG = "NecessidadeEspecialAPI";

    public NecessidadeEspecialAPI(final Base b) {
        super(b);
    }

    public void getNecessidadesEspeciais(final ApiResponse r)
    {
        Call<ResponseData<List<NecessidadeEspecialData>>> call = new NecessidadeEspecialRequests().getService().getAll("nome,asc", this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<NecessidadeEspecialData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<NecessidadeEspecialData>>> call, Response<ResponseData<List<NecessidadeEspecialData>>> response) {
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
            public void onFailure(Call<ResponseData<List<NecessidadeEspecialData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getNecessidadesEspeciais] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

}
