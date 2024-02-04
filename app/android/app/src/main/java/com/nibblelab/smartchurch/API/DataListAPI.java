package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.DataListRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.DataListData;
import com.nibblelab.smartchurch.model.ResponseData;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DataListAPI extends BaseAPI {
    public final String TAG = "DataListAPI";

    public DataListAPI(final Base b) {
        super(b);
    }

    public void getDataLists(final ApiResponse r)
    {
        Call<ResponseData<DataListData>> call = new DataListRequests().getService().dataLists(this.getAuthToken());
        call.enqueue(new Callback<ResponseData<DataListData>>() {

            @Override
            public void onResponse(Call<ResponseData<DataListData>> call, Response<ResponseData<DataListData>> response) {
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
            public void onFailure(Call<ResponseData<DataListData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getDataLists] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
}
