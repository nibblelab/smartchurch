package com.nibblelab.smartchurch.API.services;

import com.nibblelab.smartchurch.model.RegisterData;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.UserData;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

public interface UserService {
    @GET("public/login")
    Call<ResponseData<UserData>> login(@Query("email") String email, @Query("pass") String senha, @Query("is_api") boolean is_api);

    @GET("public/requestPwdReset")
    Call<ResponseData<Object>> passwordReset(@Query("email") String email);

    @POST("public/register")
    Call<ResponseData<Object>> register(@Body RegisterData data);
}
