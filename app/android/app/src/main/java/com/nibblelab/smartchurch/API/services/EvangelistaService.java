package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.OficialData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface EvangelistaService {
    @GET("evangelistas/all")
    Call<ResponseData<List<OficialData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                 @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                 @Query("igreja") String igreja, @Query("stat") String stat,
                                                 @Query("disponibilidade") String disponibilidade, @Query("pessoa") String pessoa,
                                                 @Query("sexo") String sexo, @Query("estado_civil") String estado_civil,
                                                 @Query("escolaridade") String escolaridade, @Query("com_filhos") boolean com_filhos,
                                                 @Query("sem_filhos") boolean sem_filhos, @Header("Authorization") String authToken);
}
