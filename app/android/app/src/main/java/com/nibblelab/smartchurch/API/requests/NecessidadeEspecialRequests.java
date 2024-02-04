package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.NecessidadeEspecialService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class NecessidadeEspecialRequests {

    private final Retrofit retrofit;

    public NecessidadeEspecialRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public NecessidadeEspecialService getService() { return this.retrofit.create(NecessidadeEspecialService.class); }
}
