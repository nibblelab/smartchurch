package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.MinisterioRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.MinisterioData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class MinisterioAPI extends BaseAPI {
    public final String TAG = "MinisterioAPI";

    public MinisterioAPI(final Base b) {
        super(b);
    }

    public void getMinisterios(String page, String pagesize, String searchBy, String orderBy, String stat, String igreja, final ApiResponse r)
    {
        Call<ResponseData<List<MinisterioData>>> call = new MinisterioRequests().getService().getAll(page, pagesize, searchBy, orderBy, stat, igreja, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<MinisterioData>>> () {
            @Override
            public void onResponse(Call<ResponseData<List<MinisterioData>>> call, Response<ResponseData<List<MinisterioData>>> response) {
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
            public void onFailure(Call<ResponseData<List<MinisterioData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getMinisterios] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllMinisterios(final ApiResponse r)
    {
        getMinisterios("", "", "", "", "", "", r);
    }

    public void getAllMinisteriosForIgreja(String igreja, final ApiResponse r)
    {
        getMinisterios("", "", "", "", "", igreja, r);
    }
}
