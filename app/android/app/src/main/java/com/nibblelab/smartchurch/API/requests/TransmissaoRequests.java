package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.TransmissaoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class TransmissaoRequests {

    private final Retrofit retrofit;

    public TransmissaoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public TransmissaoService getService() { return this.retrofit.create(TransmissaoService.class); }
}
