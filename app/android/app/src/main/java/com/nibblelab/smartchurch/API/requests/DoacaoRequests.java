package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.DoacaoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class DoacaoRequests {

    private final Retrofit retrofit;

    public DoacaoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public DoacaoService getService() { return this.retrofit.create(DoacaoService.class); }
}
