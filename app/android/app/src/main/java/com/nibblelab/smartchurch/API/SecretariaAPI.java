package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.SecretariaRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SecretariaData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SecretariaAPI extends BaseAPI {
    public final String TAG = "SecretariaAPI";

    public SecretariaAPI(final Base b) {
        super(b);
    }

    public void getSecretarias(String page, String pagesize, String searchBy, String orderBy, String stat, String ref_tp,
                               String ref, String igreja, String sociedade, String ministerio, final ApiResponse r)
    {
        Call<ResponseData<List<SecretariaData>>> call = new SecretariaRequests().getService().getAll(page, pagesize, searchBy, orderBy, stat, ref_tp,
                ref, igreja, sociedade, ministerio, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<SecretariaData>>>() {
            @Override
            public void onResponse(Call<ResponseData<List<SecretariaData>>> call, Response<ResponseData<List<SecretariaData>>> response) {
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
            public void onFailure(Call<ResponseData<List<SecretariaData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getSecretarias] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllSecretarias(final ApiResponse r)
    {
        getSecretarias("", "", "", "", "", "",
                "", "", "", "", r);
    }

    public void getAllSecretariasForIgreja(String igreja, final ApiResponse r)
    {
        getSecretarias("", "", "", "", "", "",
                "", igreja, "", "", r);
    }
}
