package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.AgendaData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface AgendaService {

    @GET("agendas/all")
    Call<ResponseData<List<AgendaData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                @Query("stat") String stat, @Query("ref") String ref, @Query("ref_tp") String ref_tp,
                                                @Query("responsavel") String responsavel, @Query("inicio") String inicio,
                                                @Query("termino") String termino, @Query("recorrente") boolean recorrente,
                                                @Query("domingo") boolean domingo, @Query("segunda") boolean segunda,
                                                @Query("terca") boolean terca, @Query("quarta") boolean quarta,
                                                @Query("quinta") boolean quinta, @Query("sexta") boolean sexta,
                                                @Query("sabado") boolean sabado, @Query("tags") String tags,
                                                @Query("igreja") String igreja, @Header("Authorization") String authToken);
}
