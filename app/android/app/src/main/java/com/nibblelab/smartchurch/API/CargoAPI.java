package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.CargoRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.CargoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CargoAPI extends BaseAPI {
    public final String TAG = "CargoAPI";

    public CargoAPI(final Base b) {
        super(b);
    }

    public void getCargos(String page, String pagesize, String searchBy, String orderBy, String instancia, String perfil, final ApiResponse r)
    {
        Call<ResponseData<List<CargoData>>> call = new CargoRequests().getService().getAll(page, pagesize, searchBy, orderBy, instancia, perfil, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<CargoData>>> () {
            @Override
            public void onResponse(Call<ResponseData<List<CargoData>>> call, Response<ResponseData<List<CargoData>>> response) {
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
            public void onFailure(Call<ResponseData<List<CargoData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getCargos] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllCargos(final ApiResponse r)
    {
        getCargos("", "", "", "", "", "", r);
    }
}
