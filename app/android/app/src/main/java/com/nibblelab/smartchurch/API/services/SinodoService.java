package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SinodoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SinodoService {

    @GET("sinodos/all")
    Call<ResponseData<List<SinodoData>>> getAll(@Query("searchBy") String searchBy, @Query("stat") String stat, @Header("Authorization") String authToken);
}
