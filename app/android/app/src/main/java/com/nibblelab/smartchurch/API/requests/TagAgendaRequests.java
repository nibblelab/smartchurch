package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.TagAgendaService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class TagAgendaRequests {
    private final Retrofit retrofit;

    public TagAgendaRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public TagAgendaService getService()
    {
        return this.retrofit.create(TagAgendaService.class);
    }
}
