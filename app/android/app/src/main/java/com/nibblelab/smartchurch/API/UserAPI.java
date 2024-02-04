package com.nibblelab.smartchurch.API;

import android.util.Log;

import com.nibblelab.smartchurch.API.requests.UserRequests;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.model.LoginData;
import com.nibblelab.smartchurch.model.RegisterData;
import com.nibblelab.smartchurch.model.ResponseData;
import com.nibblelab.smartchurch.model.UserData;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class UserAPI extends BaseAPI {

    public static final String TAG = "UserAPI";

    public UserAPI(final Base b)
    {
        super(b);
    }

    public void login(LoginData login, final ApiResponse r)
    {
        Call<ResponseData<UserData>> call = new UserRequests().getService().login(login.getEmail(), login.getSenha(), true);
        call.enqueue(new Callback<ResponseData<UserData>>() {
            @Override
            public void onResponse(Call<ResponseData<UserData>> call, Response<ResponseData<UserData>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            b.setUserToken(data.getToken());
                            r.onResponse(data.getData());
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<UserData>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[login] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void resetPwd(String email, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new UserRequests().getService().passwordReset(email);
        call.enqueue(new Callback<ResponseData<Object>>() {

            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[resetPwd] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }

    public void register(RegisterData register, final ApiResponse r)
    {
        Call<ResponseData<Object>> call = new UserRequests().getService().register(register);
        call.enqueue(new Callback<ResponseData<Object>>() {

            @Override
            public void onResponse(Call<ResponseData<Object>> call, Response<ResponseData<Object>> response) {
                try {
                    if(response.errorBody() != null) {
                        r.onFail(response.errorBody());
                    }
                    else {
                        ResponseData data = response.body();
                        if(data.getSuccess()) {
                            r.onResponse();
                        }
                        else {
                            r.onError(data.getMsg());
                        }
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onFailure(Call<ResponseData<Object>> call, Throwable t) {
                try {
                    r.onFail(t.getMessage());
                    Log.e(TAG, "[register] Erro: " + t.getMessage());
                    checkNoInternetException(t.getMessage());
                } catch (Exception e) {

                }
            }
        });
    }
}
