package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.TransmissaoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.Status;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TransmissaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TransmissaoAPI extends BaseAPI {

    public final String TAG = "TransmissaoAPI";

    public TransmissaoAPI(final Base b) {
        super(b);
    }

    public void getTransmissoesDaIgreja(String igreja, String stat, final ApiResponse r)
    {
        Call<ResponseData<List<TransmissaoData>>> call = new TransmissaoRequests().getService().getAllFromIgreja(stat, igreja, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<TransmissaoData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<TransmissaoData>>> call, Response<ResponseData<List<TransmissaoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<TransmissaoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getTransmissoesDaIgreja] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getTransmissoesAtivasDaIgreja(String igreja, final ApiResponse r)
    {
        this.getTransmissoesDaIgreja(igreja, Status.ATIVO, r);
    }
}
