package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.PessoaData;
import com.nibblelab.smartchurch.model.ResponseData;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface PessoaService {

    @GET("pessoas/me")
    Call<ResponseData<PessoaData<String>>> mine(@Query("id") String id, @Header("Authorization") String authToken);

    @GET("pessoas/relPerfilPreenchido")
    Call<ResponseData<Float>> perfilPreenchido(@Query("id") String id, @Header("Authorization") String authToken);

    @PUT("pessoas/edit/{id}")
    Call<ResponseData<Object>> edit(@Path("id") String id, @Body PessoaData data, @Header("Authorization") String authToken);
}
