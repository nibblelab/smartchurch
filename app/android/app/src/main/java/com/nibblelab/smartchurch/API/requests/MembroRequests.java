package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.MembroService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class MembroRequests {

    private final Retrofit retrofit;

    public MembroRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public MembroService getService() { return this.retrofit.create(MembroService.class); }
}
