package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.PresbiterioService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class PresbiterioRequests {

    private final Retrofit retrofit;

    public PresbiterioRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public PresbiterioService getService() { return this.retrofit.create(PresbiterioService.class); }
}
