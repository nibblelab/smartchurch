package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.OficialService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class OficialRequests {
    private final Retrofit retrofit;

    public OficialRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public OficialService getService() {
        return this.retrofit.create(OficialService.class);
    }
}
