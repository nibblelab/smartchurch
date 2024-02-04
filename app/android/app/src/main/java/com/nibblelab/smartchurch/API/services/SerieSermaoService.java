package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SerieSermaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SerieSermaoService {

    @GET("seriesdesermoes/all")
    Call<ResponseData<List<SerieSermaoData>>> getAll(@Query("igreja") String igreja, @Query("stat") String stat,
                                                     @Query("orderBy") String orderBy, @Header("Authorization") String authToken);
}
