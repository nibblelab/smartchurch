package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.TagAgendaData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Query;

public interface TagAgendaService {

    @GET("tagsagenda/all")
    Call<ResponseData<List<TagAgendaData>>> getAll(@Query("page") String page, @Query("pageSize") String pagesize,
                                                   @Query("searchBy") String searchBy, @Query("orderBy") String orderBy,
                                                   @Query("stat") String stat, @Query("contextos") String contextos, @Header("Authorization") String authToken);
}
