package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.EstudoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface EstudoService {

    @GET("estudos/me")
    Call<ResponseData<EstudoData>> get(@Query("id") String id, @Header("Authorization") String authToken);

    @GET("estudos/all")
    Call<ResponseData<List<EstudoData>>> getAll(@Query("igreja") String igreja, @Query("serie") String serie,
                                                @Query("stat") String stat, @Query("destinatarios") String destinatarios,
                                                @Query("publicado_apos") String publicado_apos, @Query("searchBy") String searchBy,
                                                @Query("orderBy") String orderBy, @Query("page") String page,
                                                @Query("pageSize") String pageSize, @Header("Authorization") String authToken);
}
