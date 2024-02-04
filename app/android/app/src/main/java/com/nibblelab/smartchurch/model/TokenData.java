package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class TokenData {

    @SerializedName("token")
    private String token;

    public TokenData() {
        this.token = "";
    }

    public TokenData(String token) {
        this.token = token;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }
}
