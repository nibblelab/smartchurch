package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ProfissaoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface ProfissaoService {
    @GET("profissoes/all")
    Call<ResponseData<List<ProfissaoData>>> getAll(@Query("orderBy") String orderBy, @Header("Authorization") String authToken);
}
