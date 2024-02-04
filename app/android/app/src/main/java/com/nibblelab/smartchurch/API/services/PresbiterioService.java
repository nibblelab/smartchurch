package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.PresbiterioData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface PresbiterioService {

    @GET("presbiterios/all")
    Call<ResponseData<List<PresbiterioData>>> getAll(@Query("searchBy") String searchBy, @Query("stat") String stat,
                                                     @Query("sinodo") String sinodo, @Header("Authorization") String authToken);
}
