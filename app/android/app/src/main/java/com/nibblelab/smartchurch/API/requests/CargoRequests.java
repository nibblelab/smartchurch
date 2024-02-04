package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.CargoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class CargoRequests {
    private final Retrofit retrofit;

    public CargoRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public CargoService getService()
    {
        return this.retrofit.create(CargoService.class);
    }
}
