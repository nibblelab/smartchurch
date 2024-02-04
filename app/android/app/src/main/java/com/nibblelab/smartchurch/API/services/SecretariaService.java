package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.SecretariaData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface SecretariaService {
    @GET("secretarias/all")
    Call<ResponseData<List<SecretariaData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                    @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                    @Query("stat") String stat, @Query("ref_tp") String ref_tp,
                                                    @Query("ref") String ref, @Query("igreja") String igreja,
                                                    @Query("sociedade") String sociedade, @Query("ministerio") String ministerio,
                                                    @Header("Authorization") String authToken);
}
