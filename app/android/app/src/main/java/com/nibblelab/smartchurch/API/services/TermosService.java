package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;

import retrofit2.Call;
import retrofit2.http.GET;

public interface TermosService {
    @GET("public/termos")
    Call<ResponseData<String>> termos();
}
