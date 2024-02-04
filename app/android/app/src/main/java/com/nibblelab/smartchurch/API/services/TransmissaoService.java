package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TransmissaoData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface TransmissaoService {

    @GET("transmissoes/all")
    Call<ResponseData<List<TransmissaoData>>> getAllFromIgreja(@Query("stat") String stat, @Query("igreja") String igreja, @Header("Authorization") String authToken);
}
