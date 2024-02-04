package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.BookmarkRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.BookmarkData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class BookmarkAPI extends BaseAPI {

    public final String TAG = "BookmarkAPI";

    public BookmarkAPI(final Base b) {
        super(b);
    }

    public void createForMural(BookmarkData d, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new BookmarkRequests().getService().createForMural(d, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<Object>>() {

            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[createForMural] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void remove(String id, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new BookmarkRequests().getService().remove(id, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<Object>>() {

            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[remove] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
    
    public void getBookmarks(String page, String pageSize, String searchBy, String orderBy,
                             String pessoa, String ref, String ref_tp, final ApiResponse r)
    {
        Call<ResponseData<List<BookmarkData>>> call = new BookmarkRequests().getService().getAll(page, pageSize, searchBy, orderBy, pessoa, ref, ref_tp, this.getAuthToken());
        call.enqueue(new Callback<ResponseData<List<BookmarkData>>>() {

            @Override
            public void onResponse(Call<ResponseData<List<BookmarkData>>> call, Response<ResponseData<List<BookmarkData>>> response) {
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
            public void onFailure(Call<ResponseData<List<BookmarkData>>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[getBookmarks] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }


}
