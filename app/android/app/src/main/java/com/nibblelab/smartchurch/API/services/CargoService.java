package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.CargoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface CargoService {
    @GET("cargos/all")
    Call<ResponseData<List<CargoData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                               @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                               @Query("instancia") String instancia, @Query("perfil") String perfil,
                                               @Header("Authorization") String authToken);
}
