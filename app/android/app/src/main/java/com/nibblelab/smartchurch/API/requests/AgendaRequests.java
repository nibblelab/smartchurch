package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.AgendaService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class AgendaRequests {
    private final Retrofit retrofit;

    public AgendaRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public AgendaService getService()
    {
        return this.retrofit.create(AgendaService.class);
    }
}
