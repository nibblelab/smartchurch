package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.PastorService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class PastorRequests {
    private final Retrofit retrofit;

    public PastorRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public PastorService getService() {
        return this.retrofit.create(PastorService.class);
    }
}
