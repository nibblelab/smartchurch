package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.TemploService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class TemploRequests {

    private final Retrofit retrofit;

    public TemploRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public TemploService getService() { return this.retrofit.create(TemploService.class); }
}
