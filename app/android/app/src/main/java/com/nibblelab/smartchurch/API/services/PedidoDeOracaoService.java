package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.PedidoDeOracaoData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface PedidoDeOracaoService {

    @POST("pedidosoracao/createForIgreja")
    Call<ResponseData<Object>> create(@Body PedidoDeOracaoData data, @Header("Authorization") String authToken);

    @PUT("pedidosoracao/edit/{id}")
    Call<ResponseData<Object>> edit(@Path("id") String id, @Body PedidoDeOracaoData data, @Header("Authorization") String authToken);

    @GET("pedidosoracao/all")
    Call<ResponseData<List<PedidoDeOracaoData>>> getAll(@Query("igreja") String igreja, @Query("stat") String stat,
                                                        @Query("searchBy") String searchBy,
                                                        @Query("orderBy") String orderBy, @Query("page") String page,
                                                        @Query("pageSize") String pageSize, @Header("Authorization") String authToken);
    @DELETE("pedidosoracao/remove/{id}")
    Call<ResponseData<Object>> delete(@Path("id") String id, @Header("Authorization") String authToken);

}
