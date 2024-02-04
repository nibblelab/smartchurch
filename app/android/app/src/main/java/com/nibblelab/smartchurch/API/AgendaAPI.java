package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.AgendaRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.AgendaData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AgendaAPI extends BaseAPI {
    public final String TAG = "AgendaAPI";

    public AgendaAPI(final Base b) {
        super(b);
    }

    public void getAgenda(String page, String pagesize, String searchBy, String orderBy,
                          String stat, String ref, String ref_tp,
                          String responsavel, String inicio, String termino,
                          boolean recorrente, boolean domingo, boolean segunda,
                          boolean terca, boolean quarta, boolean quinta, boolean sexta,
                          boolean sabado, String tags, String igreja, final ApiResponse r)
    {
        Call<ResponseData<List<AgendaData>>> call = new AgendaRequests().getService().getAll(page, pagesize, searchBy, orderBy,
                                                                            stat, ref, ref_tp, responsavel, inicio, termino,
                                                                            recorrente, domingo, segunda, terca, quarta, quinta, sexta,
                                                                            sabado, tags, igreja, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<AgendaData>>> () {
            @Override
            public void onResponse(Call<ResponseData<List<AgendaData>>> call, Response<ResponseData<List<AgendaData>>> response) {
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
            public void onFailure(Call<ResponseData<List<AgendaData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getAgenda] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllAgendaForIgreja(String igreja, final ApiResponse r)
    {
        getAgenda("", "", "", "","", "", "",
                "", "", "",false, false, false,
                false, false, false, false,false, "", igreja, r);
    }
}
