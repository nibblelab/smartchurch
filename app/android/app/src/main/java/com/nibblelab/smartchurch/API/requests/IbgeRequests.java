package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.IbgeService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class IbgeRequests {
    private final Retrofit retrofit;

    public IbgeRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.IBGE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public IbgeService getService()
    {
        return this.retrofit.create(IbgeService.class);
    }
}
