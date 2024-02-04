package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.EvangelistaService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class EvangelistaRequests {
    private final Retrofit retrofit;

    public EvangelistaRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public EvangelistaService getService() {
        return this.retrofit.create(EvangelistaService.class);
    }
}
