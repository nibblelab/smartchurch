package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.DiaconoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class DiaconoRequests {
    private final Retrofit retrofit;

    public DiaconoRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public DiaconoService getService() {
        return this.retrofit.create(DiaconoService.class);
    }
}
