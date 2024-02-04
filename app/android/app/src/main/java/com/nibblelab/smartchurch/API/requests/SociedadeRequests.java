package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SociedadeService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SociedadeRequests {
    private final Retrofit retrofit;

    public SociedadeRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SociedadeService getService() {
        return this.retrofit.create(SociedadeService.class);
    }
}
