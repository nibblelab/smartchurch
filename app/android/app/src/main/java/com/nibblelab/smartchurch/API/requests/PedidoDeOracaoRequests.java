package com.nibblelab.smartchurch.API.requests;

import com.nibblelab.smartchurch.API.contants.ApiContants;
import com.nibblelab.smartchurch.API.services.PedidoDeOracaoService;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class PedidoDeOracaoRequests {
    private final Retrofit retrofit;

    public PedidoDeOracaoRequests()
    {
        retrofit = new retrofit2.Retrofit.Builder()
                .baseUrl(ApiContants.BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
    }

    public PedidoDeOracaoService getService() { return this.retrofit.create(PedidoDeOracaoService.class); }
}
