package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.PresbiterioRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.PresbiterioData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PresbiterioAPI extends BaseAPI {

    public final String TAG = "PresbiterioAPI";

    public PresbiterioAPI(final Base b) {
        super(b);
    }

    public void getPresbiterios(String searchBy, String stat, String sinodo, final ApiResponse r)
    {
        Call<ResponseData<List<PresbiterioData>>> call = new PresbiterioRequests().getService().getAll(searchBy, stat, sinodo, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<PresbiterioData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<PresbiterioData>>> call, Response<ResponseData<List<PresbiterioData>>> response) {
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
            public void onFailure(Call<ResponseData<List<PresbiterioData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getPresbiterios] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getPresbiterios(final ApiResponse r)
    {
        this.getPresbiterios("", "", "", r);
    }

    public void getPresbiteriosAtivos(final ApiResponse r)
    {
        this.getPresbiterios("", Status.ATIVO, "", r);
    }

    public void getPresbiteriosDoSinodo(String sinodo, String stat, final ApiResponse r)
    {
        this.getPresbiterios("", stat, sinodo, r);
    }

    public void getPresbiteriosAtivosDoSinodo(String sinodo, final ApiResponse r)
    {
        this.getPresbiterios("", Status.ATIVO, sinodo, r);
    }

}
