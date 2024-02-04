package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.DataListData;
import com.nibblelab.smartchurch.model.ResponseData;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;

public interface DataListService {

    @GET("data/all")
    Call<ResponseData<DataListData>> dataLists(@Header("Authorization") String authToken);
}
