package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SociedadeRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SociedadeData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SociedadeAPI extends BaseAPI {
    public final String TAG = "SociedadeAPI";

    public SociedadeAPI(final Base b) {
        super(b);
    }

    public void getSociedades(String page, String pagesize, String searchBy, String orderBy, String stat, String reference, String igreja,
                              String federacao, String sinodal, String nacional, final ApiResponse r)
    {
        Call<ResponseData<List<SociedadeData>>> call = new SociedadeRequests().getService().getAll(page, pagesize, searchBy, orderBy, stat, reference, igreja,
                federacao, sinodal, nacional, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SociedadeData>>>() {
            @Override
            public void onResponse(Call<ResponseData<List<SociedadeData>>> call, Response<ResponseData<List<SociedadeData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SociedadeData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSociedades] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllSociedades(final ApiResponse r)
    {
        getSociedades("", "", "", "", "", "", "",
                "", "", "", r);
    }

    public void getAllSociedadesForIgreja(String igreja, final ApiResponse r)
    {
        getSociedades("", "", "", "", "", "", igreja,
                "", "", "", r);
    }
}
