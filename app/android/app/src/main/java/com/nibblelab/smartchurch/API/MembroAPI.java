package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.MembroRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.MembroData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MembroAPI extends BaseAPI {

    public final String TAG = "MembroAPI";

    public MembroAPI(final Base b) {
        super(b);
    }

    public void getMe(String id, final ApiResponse r)
    {
        Call<ResponseData<MembroData>> call = new MembroRequests().getService().getMe(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<MembroData>>() {
            @Override
            public void onResponse(Call<ResponseData<MembroData>> call, Response<ResponseData<MembroData>> response) {
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
            public void onFailure(Call<ResponseData<MembroData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getMe] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void create(MembroData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new MembroRequests().getService().create(d, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<Object>>() {

            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[create] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void edit(MembroData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new MembroRequests().getService().edit(d.getId(), d, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<Object>>() {
            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[edit] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

}
