package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.DoacaoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface DoacaoService {
    @GET("doacoes/all")
    Call<ResponseData<List<DoacaoData>>> getAll(@Query("orderBy") String orderBy, @Header("Authorization") String authToken);
}
