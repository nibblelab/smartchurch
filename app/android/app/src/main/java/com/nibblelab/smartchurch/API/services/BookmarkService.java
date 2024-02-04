package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.BookmarkData;
import com.nibblelab.smartchurch.model.ResponseData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.POST;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface BookmarkService {

    @POST("bookmarks/createForMural")
    Call<ResponseData<Object>> createForMural(@Body BookmarkData data, @Header("Authorization") String authToken);

    @GET("bookmarks/all")
    Call<ResponseData<List<BookmarkData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                  @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                  @Query("pessoa") String pessoa, @Query("ref") String ref,
                                                  @Query("ref_tp") String ref_tp, @Header("Authorization") String authToken);

    @DELETE("bookmarks/remove/{id}")
    Call<ResponseData<Object>> remove(@Path("id") String id, @Header("Authorization") String authToken);
}
