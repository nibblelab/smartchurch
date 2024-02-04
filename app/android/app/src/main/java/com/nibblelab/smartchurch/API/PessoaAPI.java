package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.PessoaRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.CheckBoxData;
import com.nibblelab.smartchurch.model.PessoaData;
import com.nibblelab.smartchurch.model.ResponseData;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PessoaAPI extends BaseAPI {

    public final String TAG = "PessoaAPI";

    public PessoaAPI(final Base b) {
        super(b);
    }

    public void getMe(String id, final ApiResponse r) {
        Call<ResponseData<PessoaData<String>>> call = new PessoaRequests().getService().mine(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<PessoaData<String>>>() {
            @Override
            public void onResponse(Call<ResponseData<PessoaData<String>>> call, Response<ResponseData<PessoaData<String>>> response) {
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
            public void onFailure(Call<ResponseData<PessoaData<String>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getMe] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getPerfilPreenchido(String id, final ApiResponse r) {
        Call<ResponseData<Float>> call = new PessoaRequests().getService().perfilPreenchido(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<Float>>() {

            @Override
            public void onResponse(Call<ResponseData<Float>> call, Response<ResponseData<Float>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse(data.getData());
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Float>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getPerfilPreenchido] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void save(PessoaData<CheckBoxData> data, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new PessoaRequests().getService().edit(data.getId(), data, this.getAuthToken());
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
                    Log.e(TAG, "[save] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
}
