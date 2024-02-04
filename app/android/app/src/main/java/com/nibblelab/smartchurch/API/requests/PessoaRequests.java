package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.PessoaService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class PessoaRequests {

    private final Retrofit retrofit;

    public PessoaRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public PessoaService getService()
    {
        return this.retrofit.create(PessoaService.class);
    }
}
