package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.MuralData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface MuralService {

    @GET("mural/me")
    Call<ResponseData<MuralData>> get(@Query("id") String id, @Header("Authorization") String authToken);

    @GET("mural/all")
    Call<ResponseData<List<MuralData>>> getAll(@Query("ref") String ref, @Query("ref_tp") String ref_tp,
                                                @Query("chave") String chave, @Query("publicado_apos") String publicado_apos,
                                                @Query("destinatarios") String destinatarios, @Query("stat") String stat,
                                                @Query("igreja") String igreja, @Query("pessoa") String pessoa,
                                                @Query("video") boolean video, @Query("audio") boolean audio,
                                                @Query("bookmarded") boolean bookmarded,
                                                @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                @Query("page") String page, @Query("pageSize") String pageSize,
                                                @Header("Authorization") String authToken);
}
