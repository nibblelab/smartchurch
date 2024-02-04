package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.SermaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SermaoService {

    @GET("sermoes/me")
    Call<ResponseData<SermaoData>> get(@Query("id") String id, @Header("Authorization") String authToken);

    @GET("sermoes/all")
    Call<ResponseData<List<SermaoData>>> getAll(@Query("igreja") String igreja, @Query("serie") String serie,
                                                @Query("stat") String stat, @Query("depois_de") String depois_de,
                                                @Query("publicado_apos") String publicado_apos, @Query("searchBy") String searchBy,
                                                @Query("orderBy") String orderBy, @Query("page") String page,
                                                @Query("pageSize") String pageSize, @Header("Authorization") String authToken);
}
