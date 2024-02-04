package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SerieSermaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SerieSermaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SerieSermaoAPI extends BaseAPI {

    public final String TAG = "SerieSermaoAPI";

    public SerieSermaoAPI(final Base b) {
        super(b);
    }

    public void getSeriesSermao(String igreja, String stat, String orderBy, final ApiResponse r)
    {
        Call<ResponseData<List<SerieSermaoData>>> call = new SerieSermaoRequests().getService().getAll(igreja, stat, orderBy, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SerieSermaoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<SerieSermaoData>>> call, Response<ResponseData<List<SerieSermaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SerieSermaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSeriesSermao] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getSeriesSermaoAtivos(String igreja, final ApiResponse r) {
        this.getSeriesSermao(igreja, Status.ATIVO, "time_cad,desc", r);
    }
}
