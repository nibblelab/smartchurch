package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SerieEstudoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SerieEstudoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SerieEstudoAPI extends BaseAPI {
    
    public final String TAG = "SerieEstudoAPI";

    public SerieEstudoAPI(final Base b) {
        super(b);
    }

    public void getSeriesEstudo(String igreja, String stat, String orderBy, final ApiResponse r)
    {
        Call<ResponseData<List<SerieEstudoData>>> call = new SerieEstudoRequests().getService().getAll(igreja, stat, orderBy, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SerieEstudoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<SerieEstudoData>>> call, Response<ResponseData<List<SerieEstudoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SerieEstudoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSeriesEstudo] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getSeriesEstudoAtivos(String igreja, final ApiResponse r) {
        this.getSeriesEstudo(igreja, Status.ATIVO, "time_cad,desc", r);
    }
}
