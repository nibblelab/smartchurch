package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.IbgeRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.UFData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class IbgeAPI extends BaseAPI {

    public static final String TAG = "IbgeAPI";

    public IbgeAPI(final Base b)
    {
        super(b);
    }

    public void estados(final ApiResponse r)
    {
        Call<List<UFData>> call = new IbgeRequests().getService().estados();
        call.enqueue(new Callback<List<UFData>>() {
            @Override
            public void onResponse(Call<List<UFData>> call, Response<List<UFData>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        List<UFData> data = response.body();
                        r.onResponse(data);
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<List<UFData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[estados] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
}
