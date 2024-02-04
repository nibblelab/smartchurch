package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.UserService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class UserRequests {
    private final Retrofit retrofit;

    public UserRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public UserService getService()
    {
        return this.retrofit.create(UserService.class);
    }
}
