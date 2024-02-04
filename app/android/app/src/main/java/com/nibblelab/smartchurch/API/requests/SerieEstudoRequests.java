package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SerieEstudoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SerieEstudoRequests {

    private final Retrofit retrofit;

    public SerieEstudoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SerieEstudoService getService() { return this.retrofit.create(SerieEstudoService.class); }
}
