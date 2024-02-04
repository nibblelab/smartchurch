package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TemploData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface TemploService {

    @GET("templos/all")
    Call<ResponseData<List<TemploData>>> getAll(@Query("searchBy") String searchBy, @Query("stat") String stat,
                                                @Query("sinodo") String sinodo, @Query("presbiterio") String presbiterio,
                                                @Query("igreja") String igreja, @Header("Authorization") String authToken);
}
