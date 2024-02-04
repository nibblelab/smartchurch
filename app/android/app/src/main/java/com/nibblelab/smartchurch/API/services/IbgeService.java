package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.MunicipioData;
import com.nibblelab.smartchurch.model.UFData;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Path;

public interface IbgeService {
    @GET("localidades/estados")
    Call<List<UFData>> estados();

    @GET("localidades/estados/{uf}/municipios")
    Call<List<MunicipioData>> municipios(@Path("uf") int uf_id);
}
