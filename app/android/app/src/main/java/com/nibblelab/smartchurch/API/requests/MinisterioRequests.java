package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.MinisterioService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class MinisterioRequests {
    private final Retrofit retrofit;

    public MinisterioRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public MinisterioService getService() {
        return this.retrofit.create(MinisterioService.class);
    }
}
