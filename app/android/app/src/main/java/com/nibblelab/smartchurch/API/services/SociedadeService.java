package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.SociedadeData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SociedadeService {
    @GET("sociedades/all")
    Call<ResponseData<List<SociedadeData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                 @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                 @Query("stat") String stat, @Query("reference") String reference, @Query("igreja") String igreja,
                                                 @Query("federacao") String federacao, @Query("sinodal") String sinodal,
                                                 @Query("nacional") String nacional, @Header("Authorization") String authToken);
}
