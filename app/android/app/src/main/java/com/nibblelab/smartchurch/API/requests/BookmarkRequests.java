package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.BookmarkService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class BookmarkRequests {

    private final Retrofit retrofit;

    public BookmarkRequests() {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public BookmarkService getService() { return this.retrofit.create(BookmarkService.class); }
}
