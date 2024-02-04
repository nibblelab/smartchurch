package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.PedidoDeOracaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.PedidoDeOracaoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class PedidoDeOracaoAPI extends BaseAPI {

    public final String TAG = "PedidoDeOracaoAPI";

    public PedidoDeOracaoAPI(final Base b) {
        super(b);
    }

    public void create(PedidoDeOracaoData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new PedidoDeOracaoRequests().getService().create(d, this.getAuthToken());
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

    public void edit(PedidoDeOracaoData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new PedidoDeOracaoRequests().getService().edit(d.getId(), d, this.getAuthToken());
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

    public void getPedidosDeOracao(String igreja, String stat, String searchBy, String orderBy, String page, String pageSize, final ApiResponse r)
    {
        Call<ResponseData<List<PedidoDeOracaoData>>> call = new PedidoDeOracaoRequests().getService().getAll(igreja, stat, searchBy, orderBy, page, pageSize, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<PedidoDeOracaoData>>>() {
            @Override
            public void onResponse(Call<ResponseData<List<PedidoDeOracaoData>>> call, Response<ResponseData<List<PedidoDeOracaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<PedidoDeOracaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getPedidosDeOracao] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getPedidosDeOracaoDaIgreja(String igreja, int page, int pageSize, final ApiResponse r)
    {
        String page_str = Integer.toString(page);
        String pageSize_str = Integer.toString(pageSize);
        getPedidosDeOracao(igreja, "", "", "time_cad,desc", page_str, pageSize_str, r);
    }

    public void delete(PedidoDeOracaoData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new PedidoDeOracaoRequests().getService().delete(d.getId(), this.getAuthToken());
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
