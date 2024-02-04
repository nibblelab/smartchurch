package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.MuralRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.MuralData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MuralAPI extends BaseAPI {

    public final String TAG = "MuralAPI";

    public MuralAPI(final Base b) {
        super(b);
    }

    public void getMural(String id, final ApiResponse r)
    {
        Call<ResponseData<MuralData>> call = new MuralRequests().getService().get(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<MuralData>>() {
            @Override
            public void onResponse(Call<ResponseData<MuralData>> call, Response<ResponseData<MuralData>> response) {
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
            public void onFailure(Call<ResponseData<MuralData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getMural] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getMurais(String ref, String ref_tp, String chave, String publicado_apos, String destinatarios, String stat,
                          String igreja, String pessoa, boolean video, boolean audio, boolean bookmarded, String searchBy,
                          String orderBy, String page, String pageSize, final ApiResponse r)
    {
        Call<ResponseData<List<MuralData>>> call = new MuralRequests().getService().getAll(ref, ref_tp, chave, publicado_apos, destinatarios, stat,
                                                                                                igreja, pessoa, video, audio, bookmarded, searchBy,
                                                                                                orderBy, page, pageSize, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<MuralData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<MuralData>>> call, Response<ResponseData<List<MuralData>>> response) {
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
            public void onFailure(Call<ResponseData<List<MuralData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getMurais] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getMuraisAtivosDaIgreja(String igreja, final ApiResponse r)
    {
        this.getMurais("", "", "", "", "igreja;congregacoes;pontos", Status.ATIVO, igreja,
                "", false, false, false,"", "time_cad,desc", "1", "5", r);
    }

    public void getMuraisAtivosDaIgrejaEPessoa(String igreja, String pessoa, final ApiResponse r)
    {
        this.getMurais("", "", "", "", "igreja;congregacoes;pontos", Status.ATIVO, igreja, pessoa,
                false, false, false,"", "time_cad,desc", "1", "5", r);
    }

    public void getMuraisBookmarkedDaPessoa(String pessoa, int page, int pageSize, final ApiResponse r)
    {
        String page_str = Integer.toString(page);
        String pageSize_str = Integer.toString(pageSize);
        this.getMurais("", "", "", "", "igreja;congregacoes;pontos", Status.ATIVO, "", pessoa,
                false, false, true,"", "time_cad,desc", page_str, pageSize_str, r);
    }

}
