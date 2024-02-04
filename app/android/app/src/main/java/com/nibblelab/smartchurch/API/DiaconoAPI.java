package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.DiaconoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.OficialData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;


public class DiaconoAPI extends BaseAPI {
    public final String TAG = "DiaconoAPI";

    public DiaconoAPI(final Base b) {
        super(b);
    }

    public void getDiaconos(String page, String pagesize, String searchBy, String orderBy, String igreja, String stat,
                            String disponibilidade, String pessoa, String sexo, String estado_civil, String escolaridade, boolean com_filhos,
                            boolean sem_filhos, final ApiResponse r)
    {
        Call<ResponseData<List<OficialData>>> call = new DiaconoRequests().getService().getAll(page, pagesize, searchBy, orderBy, igreja, stat,
                                                                            disponibilidade, pessoa, sexo, estado_civil, escolaridade, com_filhos,
                                                                            sem_filhos, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<OficialData>>> () {
            @Override
            public void onResponse(Call<ResponseData<List<OficialData>>> call, Response<ResponseData<List<OficialData>>> response) {
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
            public void onFailure(Call<ResponseData<List<OficialData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getDiaconos] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllDiaconos(final ApiResponse r)
    {
        getDiaconos("", "", "", "", "", "",
                "", "", "", "", "", false,
                false, r);
    }

    public void getAllDiaconosForIgreja(String igreja, final ApiResponse r)
    {
        getDiaconos("", "", "", "", igreja, "",
                "", "", "", "", "", false,
                false, r);
    }
}
