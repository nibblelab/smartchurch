package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SinodoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SinodoRequests {

    private final Retrofit retrofit;

    public SinodoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SinodoService getService()
    {
        return this.retrofit.create(SinodoService.class);
    }
}
