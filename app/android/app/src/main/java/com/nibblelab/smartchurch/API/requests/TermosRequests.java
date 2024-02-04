package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.TermosService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class TermosRequests {
    private final Retrofit retrofit;

    public TermosRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public TermosService getService()
    {
        return this.retrofit.create(TermosService.class);
    }
}
