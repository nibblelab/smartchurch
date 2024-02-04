package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.PresbiteroRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.OficialData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PresbiteroAPI extends BaseAPI {
    public final String TAG = "PresbiteroAPI";

    public PresbiteroAPI(final Base b) {
        super(b);
    }

    public void getPresbiteros(String page, String pagesize, String searchBy, String orderBy, String igreja, String stat,
                               String disponibilidade, String pessoa, String sexo, String estado_civil, String escolaridade, boolean com_filhos,
                               boolean sem_filhos, final ApiResponse r)
    {
        Call<ResponseData<List<OficialData>>> call = new PresbiteroRequests().getService().getAll(page, pagesize, searchBy, orderBy, igreja, stat,
                disponibilidade, pessoa, sexo, estado_civil, escolaridade, com_filhos,
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
                    Log.e(TAG, "[getPresbiteros] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllPresbiteros(final ApiResponse r)
    {
        getPresbiteros("", "", "", "", "", "",
                "", "", "", "", "", false,
                false, r);
    }

    public void getAllPresbiterosForIgreja(String igreja, final ApiResponse r)
    {
        getPresbiteros("", "", "", "", igreja, "",
                "", "", "", "", "", false,
                false, r);
    }
}
