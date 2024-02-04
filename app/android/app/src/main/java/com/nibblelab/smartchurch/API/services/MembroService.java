package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.MembroData;


import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface MembroService {

    @GET("membros/me")
    Call<ResponseData<MembroData>> getMe(@Query("id") String id, @Header("Authorization") String authToken);

    @POST("membros/create")
    Call<ResponseData<Object>> create(@Body MembroData data, @Header("Authorization") String authToken);

    @PUT("membros/edit/{id}")
    Call<ResponseData<Object>> edit(@Path("id") String id, @Body MembroData data, @Header("Authorization") String authToken);
}
