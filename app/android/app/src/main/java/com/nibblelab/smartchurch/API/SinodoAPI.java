package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SinodoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SinodoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SinodoAPI extends BaseAPI {

    public final String TAG = "SinodoAPI";

    public SinodoAPI(final Base b) {
        super(b);
    }

    public void getSinodos(String searchBy, String stat, final ApiResponse r)
    {
        Call<ResponseData<List<SinodoData>>> call = new SinodoRequests().getService().getAll(searchBy, stat, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SinodoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<SinodoData>>> call, Response<ResponseData<List<SinodoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SinodoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSinodos] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getSinodos(final ApiResponse r)
    {
        this.getSinodos("","", r);
    }

    public void getSinodos(String searchBy, final ApiResponse r)
    {
        this.getSinodos(searchBy,"", r);
    }

    public void getSinodosAtivos(String searchBy, final ApiResponse r)
    {
        this.getSinodos(searchBy, Status.ATIVO, r);
    }

    public void getSinodosAtivos(final ApiResponse r)
    {
        this.getSinodos("",Status.ATIVO, r);
    }
}
