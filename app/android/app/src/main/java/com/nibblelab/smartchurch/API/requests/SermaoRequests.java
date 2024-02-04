package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SermaoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SermaoRequests {

    private final Retrofit retrofit;

    public SermaoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SermaoService getService() { return this.retrofit.create(SermaoService.class); }
}
