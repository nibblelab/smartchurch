package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.MuralService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class MuralRequests {

    private final Retrofit retrofit;

    public MuralRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public MuralService getService() { return this.retrofit.create(MuralService.class); }
}
