package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SerieSermaoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SerieSermaoRequests {

    private final Retrofit retrofit;

    public SerieSermaoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SerieSermaoService getService() { return this.retrofit.create(SerieSermaoService.class); }
}
