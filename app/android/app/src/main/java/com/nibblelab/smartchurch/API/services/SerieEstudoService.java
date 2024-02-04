package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SerieEstudoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SerieEstudoService {

    @GET("seriesdeestudos/all")
    Call<ResponseData<List<SerieEstudoData>>> getAll(@Query("igreja") String igreja, @Query("stat") String stat, @Query("orderBy") String orderBy, @Header("Authorization") String authToken);
}
