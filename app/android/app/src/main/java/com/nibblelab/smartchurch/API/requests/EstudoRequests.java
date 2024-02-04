package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.EstudoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class EstudoRequests {
    
    private final Retrofit retrofit;

    public EstudoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public EstudoService getService() { return this.retrofit.create(EstudoService.class); }
}
