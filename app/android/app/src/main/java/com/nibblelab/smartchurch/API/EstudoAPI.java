package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.EstudoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.EstudoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class EstudoAPI extends BaseAPI {

    public final String TAG = "EstudoAPI";

    public EstudoAPI(final Base b) {
        super(b);
    }

    public void getEstudo(String id, final ApiResponse r)
    {
        Call<ResponseData<EstudoData>> call = new EstudoRequests().getService().get(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<EstudoData>>() {
            @Override
            public void onResponse(Call<ResponseData<EstudoData>> call, Response<ResponseData<EstudoData>> response) {
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
            public void onFailure(Call<ResponseData<EstudoData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getEstudo] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getEstudos(String igreja, String serie, String stat, String destinatarios, String publicado_apos, String searchBy, String orderBy,
                           String page, String pageSize, final ApiResponse r)
    {
        Call<ResponseData<List<EstudoData>>> call = new EstudoRequests().getService().getAll(igreja, serie, stat, destinatarios, publicado_apos, searchBy, orderBy, page, pageSize, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<EstudoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<EstudoData>>> call, Response<ResponseData<List<EstudoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<EstudoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getEstudos] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getEstudosAtivosDaIgrejaBySerie(String igreja, String serie, final ApiResponse r)
    {
        this.getEstudos(igreja, serie, Status.ATIVO, "igreja;congregacoes;pontos", "", "", "time_cad,desc", "", "5", r);
    }

    public void getEstudosAtivosDaIgrejaBySerieAndPage(String igreja, String serie, int page, int pageSize, final ApiResponse r)
    {
        String page_str = Integer.toString(page);
        String pageSize_str = Integer.toString(pageSize);
        this.getEstudos(igreja, serie, Status.ATIVO, "igreja;congregacoes;pontos", "", "","time_cad,desc", page_str, pageSize_str, r);
    }

    public void getLastEstudosAtivosDaIgreja(String igreja, final ApiResponse r)
    {
        this.getEstudos(igreja, "", Status.ATIVO, "igreja;congregacoes;pontos", "", "","time_cad,desc", "1", "5", r);
    }

    public void getLastEstudoAtivo(String igreja, final ApiResponse r)
    {
        this.getEstudos(igreja, "", Status.ATIVO, "igreja;congregacoes;pontos", "", "","time_cad,desc", "1", "1", r);
    }

    
}
