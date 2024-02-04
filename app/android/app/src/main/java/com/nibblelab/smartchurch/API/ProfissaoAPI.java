package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.ProfissaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.ProfissaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ProfissaoAPI extends BaseAPI {

    public final String TAG = "ProfissaoAPI";

    public ProfissaoAPI(final Base b) {
        super(b);
    }

    public void getProfissoes(final ApiResponse r)
    {
        Call<ResponseData<List<ProfissaoData>>> call = new ProfissaoRequests().getService().getAll("nome,asc", this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<ProfissaoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<ProfissaoData>>> call, Response<ResponseData<List<ProfissaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<ProfissaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getProfissoes] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

}
