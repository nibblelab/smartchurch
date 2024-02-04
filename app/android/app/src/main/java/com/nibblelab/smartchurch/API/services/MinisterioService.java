package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.MinisterioData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface MinisterioService {
    @GET("ministerios/all")
    Call<ResponseData<List<MinisterioData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                   @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                   @Query("stat") String stat, @Query("igreja") String igreja,
                                                   @Header("Authorization") String authToken);
}
