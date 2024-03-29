package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.DataListService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class DataListRequests {

    private final Retrofit retrofit;

    public DataListRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public DataListService getService()
    {
        return this.retrofit.create(DataListService.class);
    }
}
