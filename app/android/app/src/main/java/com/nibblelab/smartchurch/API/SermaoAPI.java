package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SermaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SermaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SermaoAPI extends BaseAPI {

    public final String TAG = "SermaoAPI";

    public SermaoAPI(final Base b) {
        super(b);
    }

    public void getSermao(String id, final ApiResponse r)
    {
        Call<ResponseData<SermaoData>> call = new SermaoRequests().getService().get(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<SermaoData>>() {
            @Override
            public void onResponse(Call<ResponseData<SermaoData>> call, Response<ResponseData<SermaoData>> response) {
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
            public void onFailure(Call<ResponseData<SermaoData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSermao] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getSermoes(String igreja, String serie, String stat, String depois_de, String publicado_apos, String searchBy, String orderBy,
                          String page, String pageSize, final ApiResponse r)
    {
        Call<ResponseData<List<SermaoData>>> call = new SermaoRequests().getService().getAll(igreja, serie, stat, depois_de, publicado_apos, searchBy, orderBy, page, pageSize, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SermaoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<SermaoData>>> call, Response<ResponseData<List<SermaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SermaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSermoes] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getSermoesAtivosOfSerie(String igreja, String serie, final ApiResponse r)
    {
        this.getSermoes(igreja, serie, Status.ATIVO, "", "", "", "time_cad,desc", "", "10", r);
    }

    public void getSermoesAtivosOfSerieByPage(String igreja, String serie, int page, int pageSize, final ApiResponse r)
    {
        String page_str = Integer.toString(page);
        String pageSize_str = Integer.toString(pageSize);
        this.getSermoes(igreja, serie, Status.ATIVO, "", "", "","time_cad,desc", page_str, pageSize_str, r);
    }

    public void getLastSermoesAtivos(String igreja, final ApiResponse r)
    {
        this.getSermoes(igreja, "", Status.ATIVO, "", "", "","time_cad,desc", "1", "10", r);
    }

    public void getSermoesAtivosApos(String igreja, String depois_de, final ApiResponse r)
    {
        this.getSermoes(igreja, "", Status.ATIVO, depois_de, "", "","time_cad,desc", "1", "10", r);
    }

    public void getSermoesAtivosPublicadosApos(String igreja, String publicado_apos, final ApiResponse r)
    {
        this.getSermoes(igreja, "", Status.ATIVO, "", publicado_apos, "","time_cad,desc", "1", "10", r);
    }

    public void getLastSermoaoAtivo(String igreja, final ApiResponse r)
    {
        this.getSermoes(igreja, "", Status.ATIVO, "", "", "","time_cad,desc", "1", "1", r);
    }

}
