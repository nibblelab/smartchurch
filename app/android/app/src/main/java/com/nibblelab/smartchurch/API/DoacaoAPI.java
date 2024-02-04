package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.DoacaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.DoacaoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DoacaoAPI extends BaseAPI {

    public final String TAG = "DoacaoAPI";

    public DoacaoAPI(final Base b) {
        super(b);
    }

    public void getDoacoes(final ApiResponse r)
    {
        Call<ResponseData<List<DoacaoData>>> call = new DoacaoRequests().getService().getAll("nome,asc", this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<DoacaoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<DoacaoData>>> call, Response<ResponseData<List<DoacaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<DoacaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getDoacoes] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
}
