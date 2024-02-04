package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.SecretariaService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class SecretariaRequests {
    private final Retrofit retrofit;

    public SecretariaRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public SecretariaService getService() {
        return this.retrofit.create(SecretariaService.class);
    }
}
