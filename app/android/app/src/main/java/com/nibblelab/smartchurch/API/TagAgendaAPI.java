package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.TagAgendaRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TagAgendaData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TagAgendaAPI extends BaseAPI {
    public final String TAG = "TagAgendaAPI";

    public TagAgendaAPI(final Base b) {
        super(b);
    }

    public void getTagsAgenda(String page, String pagesize, String searchBy, String orderBy,
                              String stat, String contextos, final ApiResponse r)
    {
        Call<ResponseData<List<TagAgendaData>>> call = new TagAgendaRequests().getService().getAll(page, pagesize, searchBy, orderBy, stat, contextos, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<TagAgendaData>>>() {
            @Override
            public void onResponse(Call<ResponseData<List<TagAgendaData>>> call, Response<ResponseData<List<TagAgendaData>>> response) {
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
            public void onFailure(Call<ResponseData<List<TagAgendaData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getTagsAgenda] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void getAllTagsAgenda(final ApiResponse r)
    {
        getTagsAgenda("", "", "", "","", "", r);
    }
}
